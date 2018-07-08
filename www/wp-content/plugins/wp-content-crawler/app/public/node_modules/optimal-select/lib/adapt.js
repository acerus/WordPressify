'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var _slicedToArray = function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"]) _i["return"](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError("Invalid attempt to destructure non-iterable instance"); } }; }();

exports.default = adapt;
/**
 * # Adapt
 *
 * Check and extend the environment for universal usage.
 */

/**
 * Modify the context based on the environment
 *
 * @param  {HTMLELement} element - [description]
 * @param  {Object}      options - [description]
 * @return {boolean}             - [description]
 */
function adapt(element, options) {

  // detect environment setup
  if (global.document) {
    return false;
  } else {
    global.document = options.context || function () {
      var root = element;
      while (root.parent) {
        root = root.parent;
      }
      return root;
    }();
  }

  // https://github.com/fb55/domhandler/blob/master/index.js#L75
  var ElementPrototype = Object.getPrototypeOf(global.document);

  // alternative descriptor to access elements with filtering invalid elements (e.g. textnodes)
  if (!Object.getOwnPropertyDescriptor(ElementPrototype, 'childTags')) {
    Object.defineProperty(ElementPrototype, 'childTags', {
      enumerable: true,
      get: function get() {
        return this.children.filter(function (node) {
          // https://github.com/fb55/domelementtype/blob/master/index.js#L12
          return node.type === 'tag' || node.type === 'script' || node.type === 'style';
        });
      }
    });
  }

  if (!Object.getOwnPropertyDescriptor(ElementPrototype, 'attributes')) {
    // https://developer.mozilla.org/en-US/docs/Web/API/Element/attributes
    // https://developer.mozilla.org/en-US/docs/Web/API/NamedNodeMap
    Object.defineProperty(ElementPrototype, 'attributes', {
      enumerable: true,
      get: function get() {
        var attribs = this.attribs;

        var attributesNames = Object.keys(attribs);
        var NamedNodeMap = attributesNames.reduce(function (attributes, attributeName, index) {
          attributes[index] = {
            name: attributeName,
            value: attribs[attributeName]
          };
          return attributes;
        }, {});
        Object.defineProperty(NamedNodeMap, 'length', {
          enumerable: false,
          configurable: false,
          value: attributesNames.length
        });
        return NamedNodeMap;
      }
    });
  }

  if (!ElementPrototype.getAttribute) {
    // https://docs.webplatform.org/wiki/dom/Element/getAttribute
    // https://developer.mozilla.org/en-US/docs/Web/API/Element/getAttribute
    ElementPrototype.getAttribute = function (name) {
      return this.attribs[name] || null;
    };
  }

  if (!ElementPrototype.getElementsByTagName) {
    // https://docs.webplatform.org/wiki/dom/Document/getElementsByTagName
    // https://developer.mozilla.org/en-US/docs/Web/API/Element/getElementsByTagName
    ElementPrototype.getElementsByTagName = function (tagName) {
      var HTMLCollection = [];
      traverseDescendants(this.childTags, function (descendant) {
        if (descendant.name === tagName || tagName === '*') {
          HTMLCollection.push(descendant);
        }
      });
      return HTMLCollection;
    };
  }

  if (!ElementPrototype.getElementsByClassName) {
    // https://docs.webplatform.org/wiki/dom/Document/getElementsByClassName
    // https://developer.mozilla.org/en-US/docs/Web/API/Element/getElementsByClassName
    ElementPrototype.getElementsByClassName = function (className) {
      var names = className.trim().replace(/\s+/g, ' ').split(' ');
      var HTMLCollection = [];
      traverseDescendants([this], function (descendant) {
        var descendantClassName = descendant.attribs.class;
        if (descendantClassName && names.every(function (name) {
          return descendantClassName.indexOf(name) > -1;
        })) {
          HTMLCollection.push(descendant);
        }
      });
      return HTMLCollection;
    };
  }

  if (!ElementPrototype.querySelectorAll) {
    // https://docs.webplatform.org/wiki/css/selectors_api/querySelectorAll
    // https://developer.mozilla.org/en-US/docs/Web/API/Element/querySelectorAll
    ElementPrototype.querySelectorAll = function (selectors) {
      var _this = this;

      selectors = selectors.replace(/(>)(\S)/g, '$1 $2').trim(); // add space for '>' selector

      // using right to left execution => https://github.com/fb55/css-select#how-does-it-work
      var instructions = getInstructions(selectors);
      var discover = instructions.shift();

      var total = instructions.length;
      return discover(this).filter(function (node) {
        var step = 0;
        while (step < total) {
          node = instructions[step](node, _this);
          if (!node) {
            // hierarchy doesn't match
            return false;
          }
          step += 1;
        }
        return true;
      });
    };
  }

  if (!ElementPrototype.contains) {
    // https://developer.mozilla.org/en-US/docs/Web/API/Node/contains
    ElementPrototype.contains = function (element) {
      var inclusive = false;
      traverseDescendants([this], function (descendant, done) {
        if (descendant === element) {
          inclusive = true;
          done();
        }
      });
      return inclusive;
    };
  }

  return true;
}

/**
 * Retrieve transformation steps
 *
 * @param  {Array.<string>}   selectors - [description]
 * @return {Array.<Function>}           - [description]
 */
function getInstructions(selectors) {
  return selectors.split(' ').reverse().map(function (selector, step) {
    var discover = step === 0;

    var _selector$split = selector.split(':'),
        _selector$split2 = _slicedToArray(_selector$split, 2),
        type = _selector$split2[0],
        pseudo = _selector$split2[1];

    var validate = null;
    var instruction = null;

    (function () {
      switch (true) {

        // child: '>'
        case />/.test(type):
          instruction = function checkParent(node) {
            return function (validate) {
              return validate(node.parent) && node.parent;
            };
          };
          break;

        // class: '.'
        case /^\./.test(type):
          var names = type.substr(1).split('.');
          validate = function validate(node) {
            var nodeClassName = node.attribs.class;
            return nodeClassName && names.every(function (name) {
              return nodeClassName.indexOf(name) > -1;
            });
          };
          instruction = function checkClass(node, root) {
            if (discover) {
              return node.getElementsByClassName(names.join(' '));
            }
            return typeof node === 'function' ? node(validate) : getAncestor(node, root, validate);
          };
          break;

        // attribute: '[key="value"]'
        case /^\[/.test(type):
          var _type$replace$split = type.replace(/\[|\]|"/g, '').split('='),
              _type$replace$split2 = _slicedToArray(_type$replace$split, 2),
              attributeKey = _type$replace$split2[0],
              attributeValue = _type$replace$split2[1];

          validate = function validate(node) {
            var hasAttribute = Object.keys(node.attribs).indexOf(attributeKey) > -1;
            if (hasAttribute) {
              // regard optional attributeValue
              if (!attributeValue || node.attribs[attributeKey] === attributeValue) {
                return true;
              }
            }
            return false;
          };
          instruction = function checkAttribute(node, root) {
            if (discover) {
              var _ret2 = function () {
                var NodeList = [];
                traverseDescendants([node], function (descendant) {
                  if (validate(descendant)) {
                    NodeList.push(descendant);
                  }
                });
                return {
                  v: NodeList
                };
              }();

              if ((typeof _ret2 === 'undefined' ? 'undefined' : _typeof(_ret2)) === "object") return _ret2.v;
            }
            return typeof node === 'function' ? node(validate) : getAncestor(node, root, validate);
          };
          break;

        // id: '#'
        case /^#/.test(type):
          var id = type.substr(1);
          validate = function validate(node) {
            return node.attribs.id === id;
          };
          instruction = function checkId(node, root) {
            if (discover) {
              var _ret3 = function () {
                var NodeList = [];
                traverseDescendants([node], function (descendant, done) {
                  if (validate(descendant)) {
                    NodeList.push(descendant);
                    done();
                  }
                });
                return {
                  v: NodeList
                };
              }();

              if ((typeof _ret3 === 'undefined' ? 'undefined' : _typeof(_ret3)) === "object") return _ret3.v;
            }
            return typeof node === 'function' ? node(validate) : getAncestor(node, root, validate);
          };
          break;

        // universal: '*'
        case /\*/.test(type):
          validate = function validate(node) {
            return true;
          };
          instruction = function checkUniversal(node, root) {
            if (discover) {
              var _ret4 = function () {
                var NodeList = [];
                traverseDescendants([node], function (descendant) {
                  return NodeList.push(descendant);
                });
                return {
                  v: NodeList
                };
              }();

              if ((typeof _ret4 === 'undefined' ? 'undefined' : _typeof(_ret4)) === "object") return _ret4.v;
            }
            return typeof node === 'function' ? node(validate) : getAncestor(node, root, validate);
          };
          break;

        // tag: '...'
        default:
          validate = function validate(node) {
            return node.name === type;
          };
          instruction = function checkTag(node, root) {
            if (discover) {
              var _ret5 = function () {
                var NodeList = [];
                traverseDescendants([node], function (descendant) {
                  if (validate(descendant)) {
                    NodeList.push(descendant);
                  }
                });
                return {
                  v: NodeList
                };
              }();

              if ((typeof _ret5 === 'undefined' ? 'undefined' : _typeof(_ret5)) === "object") return _ret5.v;
            }
            return typeof node === 'function' ? node(validate) : getAncestor(node, root, validate);
          };
      }
    })();

    if (!pseudo) {
      return instruction;
    }

    var rule = pseudo.match(/-(child|type)\((\d+)\)$/);
    var kind = rule[1];
    var index = parseInt(rule[2], 10) - 1;

    var validatePseudo = function validatePseudo(node) {
      if (node) {
        var compareSet = node.parent.childTags;
        if (kind === 'type') {
          compareSet = compareSet.filter(validate);
        }
        var nodeIndex = compareSet.findIndex(function (child) {
          return child === node;
        });
        if (nodeIndex === index) {
          return true;
        }
      }
      return false;
    };

    return function enhanceInstruction(node) {
      var match = instruction(node);
      if (discover) {
        return match.reduce(function (NodeList, matchedNode) {
          if (validatePseudo(matchedNode)) {
            NodeList.push(matchedNode);
          }
          return NodeList;
        }, []);
      }
      return validatePseudo(match) && match;
    };
  });
}

/**
 * Walking recursive to invoke callbacks
 *
 * @param {Array.<HTMLElement>} nodes   - [description]
 * @param {Function}            handler - [description]
 */
function traverseDescendants(nodes, handler) {
  nodes.forEach(function (node) {
    var progress = true;
    handler(node, function () {
      return progress = false;
    });
    if (node.childTags && progress) {
      traverseDescendants(node.childTags, handler);
    }
  });
}

/**
 * Bubble up from bottom to top
 *
 * @param  {HTMLELement} node     - [description]
 * @param  {HTMLELement} root     - [description]
 * @param  {Function}    validate - [description]
 * @return {HTMLELement}          - [description]
 */
function getAncestor(node, root, validate) {
  while (node.parent) {
    node = node.parent;
    if (validate(node)) {
      return node;
    }
    if (node === root) {
      break;
    }
  }
  return null;
}
module.exports = exports['default'];
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFkYXB0LmpzIl0sIm5hbWVzIjpbImFkYXB0IiwiZWxlbWVudCIsIm9wdGlvbnMiLCJnbG9iYWwiLCJkb2N1bWVudCIsImNvbnRleHQiLCJyb290IiwicGFyZW50IiwiRWxlbWVudFByb3RvdHlwZSIsIk9iamVjdCIsImdldFByb3RvdHlwZU9mIiwiZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9yIiwiZGVmaW5lUHJvcGVydHkiLCJlbnVtZXJhYmxlIiwiZ2V0IiwiY2hpbGRyZW4iLCJmaWx0ZXIiLCJub2RlIiwidHlwZSIsImF0dHJpYnMiLCJhdHRyaWJ1dGVzTmFtZXMiLCJrZXlzIiwiTmFtZWROb2RlTWFwIiwicmVkdWNlIiwiYXR0cmlidXRlcyIsImF0dHJpYnV0ZU5hbWUiLCJpbmRleCIsIm5hbWUiLCJ2YWx1ZSIsImNvbmZpZ3VyYWJsZSIsImxlbmd0aCIsImdldEF0dHJpYnV0ZSIsImdldEVsZW1lbnRzQnlUYWdOYW1lIiwidGFnTmFtZSIsIkhUTUxDb2xsZWN0aW9uIiwidHJhdmVyc2VEZXNjZW5kYW50cyIsImNoaWxkVGFncyIsImRlc2NlbmRhbnQiLCJwdXNoIiwiZ2V0RWxlbWVudHNCeUNsYXNzTmFtZSIsImNsYXNzTmFtZSIsIm5hbWVzIiwidHJpbSIsInJlcGxhY2UiLCJzcGxpdCIsImRlc2NlbmRhbnRDbGFzc05hbWUiLCJjbGFzcyIsImV2ZXJ5IiwiaW5kZXhPZiIsInF1ZXJ5U2VsZWN0b3JBbGwiLCJzZWxlY3RvcnMiLCJpbnN0cnVjdGlvbnMiLCJnZXRJbnN0cnVjdGlvbnMiLCJkaXNjb3ZlciIsInNoaWZ0IiwidG90YWwiLCJzdGVwIiwiY29udGFpbnMiLCJpbmNsdXNpdmUiLCJkb25lIiwicmV2ZXJzZSIsIm1hcCIsInNlbGVjdG9yIiwicHNldWRvIiwidmFsaWRhdGUiLCJpbnN0cnVjdGlvbiIsInRlc3QiLCJjaGVja1BhcmVudCIsInN1YnN0ciIsIm5vZGVDbGFzc05hbWUiLCJjaGVja0NsYXNzIiwiam9pbiIsImdldEFuY2VzdG9yIiwiYXR0cmlidXRlS2V5IiwiYXR0cmlidXRlVmFsdWUiLCJoYXNBdHRyaWJ1dGUiLCJjaGVja0F0dHJpYnV0ZSIsIk5vZGVMaXN0IiwiaWQiLCJjaGVja0lkIiwiY2hlY2tVbml2ZXJzYWwiLCJjaGVja1RhZyIsInJ1bGUiLCJtYXRjaCIsImtpbmQiLCJwYXJzZUludCIsInZhbGlkYXRlUHNldWRvIiwiY29tcGFyZVNldCIsIm5vZGVJbmRleCIsImZpbmRJbmRleCIsImNoaWxkIiwiZW5oYW5jZUluc3RydWN0aW9uIiwibWF0Y2hlZE5vZGUiLCJub2RlcyIsImhhbmRsZXIiLCJmb3JFYWNoIiwicHJvZ3Jlc3MiXSwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7a0JBYXdCQSxLO0FBYnhCOzs7Ozs7QUFNQTs7Ozs7OztBQU9lLFNBQVNBLEtBQVQsQ0FBZ0JDLE9BQWhCLEVBQXlCQyxPQUF6QixFQUFrQzs7QUFFL0M7QUFDQSxNQUFJQyxPQUFPQyxRQUFYLEVBQXFCO0FBQ25CLFdBQU8sS0FBUDtBQUNELEdBRkQsTUFFTztBQUNMRCxXQUFPQyxRQUFQLEdBQWtCRixRQUFRRyxPQUFSLElBQW9CLFlBQU07QUFDMUMsVUFBSUMsT0FBT0wsT0FBWDtBQUNBLGFBQU9LLEtBQUtDLE1BQVosRUFBb0I7QUFDbEJELGVBQU9BLEtBQUtDLE1BQVo7QUFDRDtBQUNELGFBQU9ELElBQVA7QUFDRCxLQU5vQyxFQUFyQztBQU9EOztBQUVEO0FBQ0EsTUFBTUUsbUJBQW1CQyxPQUFPQyxjQUFQLENBQXNCUCxPQUFPQyxRQUE3QixDQUF6Qjs7QUFFQTtBQUNBLE1BQUksQ0FBQ0ssT0FBT0Usd0JBQVAsQ0FBZ0NILGdCQUFoQyxFQUFrRCxXQUFsRCxDQUFMLEVBQXFFO0FBQ25FQyxXQUFPRyxjQUFQLENBQXNCSixnQkFBdEIsRUFBd0MsV0FBeEMsRUFBcUQ7QUFDbkRLLGtCQUFZLElBRHVDO0FBRW5EQyxTQUZtRCxpQkFFNUM7QUFDTCxlQUFPLEtBQUtDLFFBQUwsQ0FBY0MsTUFBZCxDQUFxQixVQUFDQyxJQUFELEVBQVU7QUFD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QUFDQUYsWUFBUTdFLElBQVIsRUFBYztBQUFBLGFBQU0rRSxXQUFXLEtBQWpCO0FBQUEsS0FBZDtBQUNBLFFBQUkvRSxLQUFLbUIsU0FBTCxJQUFrQjRELFFBQXRCLEVBQWdDO0FBQzlCN0QsMEJBQW9CbEIsS0FBS21CLFNBQXpCLEVBQW9DMEQsT0FBcEM7QUFDRDtBQUNGLEdBTkQ7QUFPRDs7QUFFRDs7Ozs7Ozs7QUFRQSxTQUFTdEIsV0FBVCxDQUFzQnZELElBQXRCLEVBQTRCWCxJQUE1QixFQUFrQzBELFFBQWxDLEVBQTRDO0FBQzFDLFNBQU8vQyxLQUFLVixNQUFaLEVBQW9CO0FBQ2xCVSxXQUFPQSxLQUFLVixNQUFaO0FBQ0EsUUFBSXlELFNBQVMvQyxJQUFULENBQUosRUFBb0I7QUFDbEIsYUFBT0EsSUFBUDtBQUNEO0FBQ0QsUUFBSUEsU0FBU1gsSUFBYixFQUFtQjtBQUNqQjtBQUNEO0FBQ0Y7QUFDRCxTQUFPLElBQVA7QUFDRCIsImZpbGUiOiJhZGFwdC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qKlxuICogIyBBZGFwdFxuICpcbiAqIENoZWNrIGFuZCBleHRlbmQgdGhlIGVudmlyb25tZW50IGZvciB1bml2ZXJzYWwgdXNhZ2UuXG4gKi9cblxuLyoqXG4gKiBNb2RpZnkgdGhlIGNvbnRleHQgYmFzZWQgb24gdGhlIGVudmlyb25tZW50XG4gKlxuICogQHBhcmFtICB7SFRNTEVMZW1lbnR9IGVsZW1lbnQgLSBbZGVzY3JpcHRpb25dXG4gKiBAcGFyYW0gIHtPYmplY3R9ICAgICAgb3B0aW9ucyAtIFtkZXNjcmlwdGlvbl1cbiAqIEByZXR1cm4ge2Jvb2xlYW59ICAgICAgICAgICAgIC0gW2Rlc2NyaXB0aW9uXVxuICovXG5leHBvcnQgZGVmYXVsdCBmdW5jdGlvbiBhZGFwdCAoZWxlbWVudCwgb3B0aW9ucykge1xuXG4gIC8vIGRldGVjdCBlbnZpcm9ubWVudCBzZXR1cFxuICBpZiAoZ2xvYmFsLmRvY3VtZW50KSB7XG4gICAgcmV0dXJuIGZhbHNlXG4gIH0gZWxzZSB7XG4gICAgZ2xvYmFsLmRvY3VtZW50ID0gb3B0aW9ucy5jb250ZXh0IHx8ICgoKSA9PiB7XG4gICAgICB2YXIgcm9vdCA9IGVsZW1lbnRcbiAgICAgIHdoaWxlIChyb290LnBhcmVudCkge1xuICAgICAgICByb290ID0gcm9vdC5wYXJlbnRcbiAgICAgIH1cbiAgICAgIHJldHVybiByb290XG4gICAgfSkoKVxuICB9XG5cbiAgLy8gaHR0cHM6Ly9naXRodWIuY29tL2ZiNTUvZG9taGFuZGxlci9ibG9iL21hc3Rlci9pbmRleC5qcyNMNzVcbiAgY29uc3QgRWxlbWVudFByb3RvdHlwZSA9IE9iamVjdC5nZXRQcm90b3R5cGVPZihnbG9iYWwuZG9jdW1lbnQpXG5cbiAgLy8gYWx0ZXJuYXRpdmUgZGVzY3JpcHRvciB0byBhY2Nlc3MgZWxlbWVudHMgd2l0aCBmaWx0ZXJpbmcgaW52YWxpZCBlbGVtZW50cyAoZS5nLiB0ZXh0bm9kZXMpXG4gIGlmICghT2JqZWN0LmdldE93blByb3BlcnR5RGVzY3JpcHRvcihFbGVtZW50UHJvdG90eXBlLCAnY2hpbGRUYWdzJykpIHtcbiAgICBPYmplY3QuZGVmaW5lUHJvcGVydHkoRWxlbWVudFByb3RvdHlwZSwgJ2NoaWxkVGFncycsIHtcbiAgICAgIGVudW1lcmFibGU6IHRydWUsXG4gICAgICBnZXQgKCkge1xuICAgICAgICByZXR1cm4gdGhpcy5jaGlsZHJlbi5maWx0ZXIoKG5vZGUpID0+IHtcbiAgICAgICAgICAvLyBodHRwczovL2dpdGh1Yi5jb20vZmI1NS9kb21lbGVtZW50dHlwZS9ibG9iL21hc3Rlci9pbmRleC5qcyNMMTJcbiAgICAgICAgICByZXR1cm4gbm9kZS50eXBlID09PSAndGFnJyB8fCBub2RlLnR5cGUgPT09ICdzY3JpcHQnIHx8IG5vZGUudHlwZSA9PT0gJ3N0eWxlJ1xuICAgICAgICB9KVxuICAgICAgfVxuICAgIH0pXG4gIH1cblxuICBpZiAoIU9iamVjdC5nZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3IoRWxlbWVudFByb3RvdHlwZSwgJ2F0dHJpYnV0ZXMnKSkge1xuICAgIC8vIGh0dHBzOi8vZGV2ZWxvcGVyLm1vemlsbGEub3JnL2VuLVVTL2RvY3MvV2ViL0FQSS9FbGVtZW50L2F0dHJpYnV0ZXNcbiAgICAvLyBodHRwczovL2RldmVsb3Blci5tb3ppbGxhLm9yZy9lbi1VUy9kb2NzL1dlYi9BUEkvTmFtZWROb2RlTWFwXG4gICAgT2JqZWN0LmRlZmluZVByb3BlcnR5KEVsZW1lbnRQcm90b3R5cGUsICdhdHRyaWJ1dGVzJywge1xuICAgICAgZW51bWVyYWJsZTogdHJ1ZSxcbiAgICAgIGdldCAoKSB7XG4gICAgICAgIGNvbnN0IHsgYXR0cmlicyB9ID0gdGhpc1xuICAgICAgICBjb25zdCBhdHRyaWJ1dGVzTmFtZXMgPSBPYmplY3Qua2V5cyhhdHRyaWJzKVxuICAgICAgICBjb25zdCBOYW1lZE5vZGVNYXAgPSBhdHRyaWJ1dGVzTmFtZXMucmVkdWNlKChhdHRyaWJ1dGVzLCBhdHRyaWJ1dGVOYW1lLCBpbmRleCkgPT4ge1xuICAgICAgICAgIGF0dHJpYnV0ZXNbaW5kZXhdID0ge1xuICAgICAgICAgICAgbmFtZTogYXR0cmlidXRlTmFtZSxcbiAgICAgICAgICAgIHZhbHVlOiBhdHRyaWJzW2F0dHJpYnV0ZU5hbWVdXG4gICAgICAgICAgfVxuICAgICAgICAgIHJldHVybiBhdHRyaWJ1dGVzXG4gICAgICAgIH0sIHsgfSlcbiAgICAgICAgT2JqZWN0LmRlZmluZVByb3BlcnR5KE5hbWVkTm9kZU1hcCwgJ2xlbmd0aCcsIHtcbiAgICAgICAgICBlbnVtZXJhYmxlOiBmYWxzZSxcbiAgICAgICAgICBjb25maWd1cmFibGU6IGZhbHNlLFxuICAgICAgICAgIHZhbHVlOiBhdHRyaWJ1dGVzTmFtZXMubGVuZ3RoXG4gICAgICAgIH0pXG4gICAgICAgIHJldHVybiBOYW1lZE5vZGVNYXBcbiAgICAgIH1cbiAgICB9KVxuICB9XG5cbiAgaWYgKCFFbGVtZW50UHJvdG90eXBlLmdldEF0dHJpYnV0ZSkge1xuICAgIC8vIGh0dHBzOi8vZG9jcy53ZWJwbGF0Zm9ybS5vcmcvd2lraS9kb20vRWxlbWVudC9nZXRBdHRyaWJ1dGVcbiAgICAvLyBodHRwczovL2RldmVsb3Blci5tb3ppbGxhLm9yZy9lbi1VUy9kb2NzL1dlYi9BUEkvRWxlbWVudC9nZXRBdHRyaWJ1dGVcbiAgICBFbGVtZW50UHJvdG90eXBlLmdldEF0dHJpYnV0ZSA9IGZ1bmN0aW9uIChuYW1lKSB7XG4gICAgICByZXR1cm4gdGhpcy5hdHRyaWJzW25hbWVdIHx8IG51bGxcbiAgICB9XG4gIH1cblxuICBpZiAoIUVsZW1lbnRQcm90b3R5cGUuZ2V0RWxlbWVudHNCeVRhZ05hbWUpIHtcbiAgICAvLyBodHRwczovL2RvY3Mud2VicGxhdGZvcm0ub3JnL3dpa2kvZG9tL0RvY3VtZW50L2dldEVsZW1lbnRzQnlUYWdOYW1lXG4gICAgLy8gaHR0cHM6Ly9kZXZlbG9wZXIubW96aWxsYS5vcmcvZW4tVVMvZG9jcy9XZWIvQVBJL0VsZW1lbnQvZ2V0RWxlbWVudHNCeVRhZ05hbWVcbiAgICBFbGVtZW50UHJvdG90eXBlLmdldEVsZW1lbnRzQnlUYWdOYW1lID0gZnVuY3Rpb24gKHRhZ05hbWUpIHtcbiAgICAgIGNvbnN0IEhUTUxDb2xsZWN0aW9uID0gW11cbiAgICAgIHRyYXZlcnNlRGVzY2VuZGFudHModGhpcy5jaGlsZFRhZ3MsIChkZXNjZW5kYW50KSA9PiB7XG4gICAgICAgIGlmIChkZXNjZW5kYW50Lm5hbWUgPT09IHRhZ05hbWUgfHwgdGFnTmFtZSA9PT0gJyonKSB7XG4gICAgICAgICAgSFRNTENvbGxlY3Rpb24ucHVzaChkZXNjZW5kYW50KVxuICAgICAgICB9XG4gICAgICB9KVxuICAgICAgcmV0dXJuIEhUTUxDb2xsZWN0aW9uXG4gICAgfVxuICB9XG5cbiAgaWYgKCFFbGVtZW50UHJvdG90eXBlLmdldEVsZW1lbnRzQnlDbGFzc05hbWUpIHtcbiAgICAvLyBodHRwczovL2RvY3Mud2VicGxhdGZvcm0ub3JnL3dpa2kvZG9tL0RvY3VtZW50L2dldEVsZW1lbnRzQnlDbGFzc05hbWVcbiAgICAvLyBodHRwczovL2RldmVsb3Blci5tb3ppbGxhLm9yZy9lbi1VUy9kb2NzL1dlYi9BUEkvRWxlbWVudC9nZXRFbGVtZW50c0J5Q2xhc3NOYW1lXG4gICAgRWxlbWVudFByb3RvdHlwZS5nZXRFbGVtZW50c0J5Q2xhc3NOYW1lID0gZnVuY3Rpb24gKGNsYXNzTmFtZSkge1xuICAgICAgY29uc3QgbmFtZXMgPSBjbGFzc05hbWUudHJpbSgpLnJlcGxhY2UoL1xccysvZywgJyAnKS5zcGxpdCgnICcpXG4gICAgICBjb25zdCBIVE1MQ29sbGVjdGlvbiA9IFtdXG4gICAgICB0cmF2ZXJzZURlc2NlbmRhbnRzKFt0aGlzXSwgKGRlc2NlbmRhbnQpID0+IHtcbiAgICAgICAgY29uc3QgZGVzY2VuZGFudENsYXNzTmFtZSA9IGRlc2NlbmRhbnQuYXR0cmlicy5jbGFzc1xuICAgICAgICBpZiAoZGVzY2VuZGFudENsYXNzTmFtZSAmJiBuYW1lcy5ldmVyeSgobmFtZSkgPT4gZGVzY2VuZGFudENsYXNzTmFtZS5pbmRleE9mKG5hbWUpID4gLTEpKSB7XG4gICAgICAgICAgSFRNTENvbGxlY3Rpb24ucHVzaChkZXNjZW5kYW50KVxuICAgICAgICB9XG4gICAgICB9KVxuICAgICAgcmV0dXJuIEhUTUxDb2xsZWN0aW9uXG4gICAgfVxuICB9XG5cbiAgaWYgKCFFbGVtZW50UHJvdG90eXBlLnF1ZXJ5U2VsZWN0b3JBbGwpIHtcbiAgICAvLyBodHRwczovL2RvY3Mud2VicGxhdGZvcm0ub3JnL3dpa2kvY3NzL3NlbGVjdG9yc19hcGkvcXVlcnlTZWxlY3RvckFsbFxuICAgIC8vIGh0dHBzOi8vZGV2ZWxvcGVyLm1vemlsbGEub3JnL2VuLVVTL2RvY3MvV2ViL0FQSS9FbGVtZW50L3F1ZXJ5U2VsZWN0b3JBbGxcbiAgICBFbGVtZW50UHJvdG90eXBlLnF1ZXJ5U2VsZWN0b3JBbGwgPSBmdW5jdGlvbiAoc2VsZWN0b3JzKSB7XG4gICAgICBzZWxlY3RvcnMgPSBzZWxlY3RvcnMucmVwbGFjZSgvKD4pKFxcUykvZywgJyQxICQyJykudHJpbSgpIC8vIGFkZCBzcGFjZSBmb3IgJz4nIHNlbGVjdG9yXG5cbiAgICAgIC8vIHVzaW5nIHJpZ2h0IHRvIGxlZnQgZXhlY3V0aW9uID0+IGh0dHBzOi8vZ2l0aHViLmNvbS9mYjU1L2Nzcy1zZWxlY3QjaG93LWRvZXMtaXQtd29ya1xuICAgICAgY29uc3QgaW5zdHJ1Y3Rpb25zID0gZ2V0SW5zdHJ1Y3Rpb25zKHNlbGVjdG9ycylcbiAgICAgIGNvbnN0IGRpc2NvdmVyID0gaW5zdHJ1Y3Rpb25zLnNoaWZ0KClcblxuICAgICAgY29uc3QgdG90YWwgPSBpbnN0cnVjdGlvbnMubGVuZ3RoXG4gICAgICByZXR1cm4gZGlzY292ZXIodGhpcykuZmlsdGVyKChub2RlKSA9PiB7XG4gICAgICAgIHZhciBzdGVwID0gMFxuICAgICAgICB3aGlsZSAoc3RlcCA8IHRvdGFsKSB7XG4gICAgICAgICAgbm9kZSA9IGluc3RydWN0aW9uc1tzdGVwXShub2RlLCB0aGlzKVxuICAgICAgICAgIGlmICghbm9kZSkgeyAvLyBoaWVyYXJjaHkgZG9lc24ndCBtYXRjaFxuICAgICAgICAgICAgcmV0dXJuIGZhbHNlXG4gICAgICAgICAgfVxuICAgICAgICAgIHN0ZXAgKz0gMVxuICAgICAgICB9XG4gICAgICAgIHJldHVybiB0cnVlXG4gICAgICB9KVxuICAgIH1cbiAgfVxuXG4gIGlmICghRWxlbWVudFByb3RvdHlwZS5jb250YWlucykge1xuICAgIC8vIGh0dHBzOi8vZGV2ZWxvcGVyLm1vemlsbGEub3JnL2VuLVVTL2RvY3MvV2ViL0FQSS9Ob2RlL2NvbnRhaW5zXG4gICAgRWxlbWVudFByb3RvdHlwZS5jb250YWlucyA9IGZ1bmN0aW9uIChlbGVtZW50KSB7XG4gICAgICB2YXIgaW5jbHVzaXZlID0gZmFsc2VcbiAgICAgIHRyYXZlcnNlRGVzY2VuZGFudHMoW3RoaXNdLCAoZGVzY2VuZGFudCwgZG9uZSkgPT4ge1xuICAgICAgICBpZiAoZGVzY2VuZGFudCA9PT0gZWxlbWVudCkge1xuICAgICAgICAgIGluY2x1c2l2ZSA9IHRydWVcbiAgICAgICAgICBkb25lKClcbiAgICAgICAgfVxuICAgICAgfSlcbiAgICAgIHJldHVybiBpbmNsdXNpdmVcbiAgICB9XG4gIH1cblxuICByZXR1cm4gdHJ1ZVxufVxuXG4vKipcbiAqIFJldHJpZXZlIHRyYW5zZm9ybWF0aW9uIHN0ZXBzXG4gKlxuICogQHBhcmFtICB7QXJyYXkuPHN0cmluZz59ICAgc2VsZWN0b3JzIC0gW2Rlc2NyaXB0aW9uXVxuICogQHJldHVybiB7QXJyYXkuPEZ1bmN0aW9uPn0gICAgICAgICAgIC0gW2Rlc2NyaXB0aW9uXVxuICovXG5mdW5jdGlvbiBnZXRJbnN0cnVjdGlvbnMgKHNlbGVjdG9ycykge1xuICByZXR1cm4gc2VsZWN0b3JzLnNwbGl0KCcgJykucmV2ZXJzZSgpLm1hcCgoc2VsZWN0b3IsIHN0ZXApID0+IHtcbiAgICBjb25zdCBkaXNjb3ZlciA9IHN0ZXAgPT09IDBcbiAgICBjb25zdCBbdHlwZSwgcHNldWRvXSA9IHNlbGVjdG9yLnNwbGl0KCc6JylcblxuICAgIHZhciB2YWxpZGF0ZSA9IG51bGxcbiAgICB2YXIgaW5zdHJ1Y3Rpb24gPSBudWxsXG5cbiAgICBzd2l0Y2ggKHRydWUpIHtcblxuICAgICAgLy8gY2hpbGQ6ICc+J1xuICAgICAgY2FzZSAvPi8udGVzdCh0eXBlKTpcbiAgICAgICAgaW5zdHJ1Y3Rpb24gPSBmdW5jdGlvbiBjaGVja1BhcmVudCAobm9kZSkge1xuICAgICAgICAgIHJldHVybiAodmFsaWRhdGUpID0+IHZhbGlkYXRlKG5vZGUucGFyZW50KSAmJiBub2RlLnBhcmVudFxuICAgICAgICB9XG4gICAgICAgIGJyZWFrXG5cbiAgICAgIC8vIGNsYXNzOiAnLidcbiAgICAgIGNhc2UgL15cXC4vLnRlc3QodHlwZSk6XG4gICAgICAgIGNvbnN0IG5hbWVzID0gdHlwZS5zdWJzdHIoMSkuc3BsaXQoJy4nKVxuICAgICAgICB2YWxpZGF0ZSA9IChub2RlKSA9PiB7XG4gICAgICAgICAgY29uc3Qgbm9kZUNsYXNzTmFtZSA9IG5vZGUuYXR0cmlicy5jbGFzc1xuICAgICAgICAgIHJldHVybiBub2RlQ2xhc3NOYW1lICYmIG5hbWVzLmV2ZXJ5KChuYW1lKSA9PiBub2RlQ2xhc3NOYW1lLmluZGV4T2YobmFtZSkgPiAtMSlcbiAgICAgICAgfVxuICAgICAgICBpbnN0cnVjdGlvbiA9IGZ1bmN0aW9uIGNoZWNrQ2xhc3MgKG5vZGUsIHJvb3QpIHtcbiAgICAgICAgICBpZiAoZGlzY292ZXIpIHtcbiAgICAgICAgICAgIHJldHVybiBub2RlLmdldEVsZW1lbnRzQnlDbGFzc05hbWUobmFtZXMuam9pbignICcpKVxuICAgICAgICAgIH1cbiAgICAgICAgICByZXR1cm4gKHR5cGVvZiBub2RlID09PSAnZnVuY3Rpb24nKSA/IG5vZGUodmFsaWRhdGUpIDogZ2V0QW5jZXN0b3Iobm9kZSwgcm9vdCwgdmFsaWRhdGUpXG4gICAgICAgIH1cbiAgICAgICAgYnJlYWtcblxuICAgICAgLy8gYXR0cmlidXRlOiAnW2tleT1cInZhbHVlXCJdJ1xuICAgICAgY2FzZSAvXlxcWy8udGVzdCh0eXBlKTpcbiAgICAgICAgY29uc3QgW2F0dHJpYnV0ZUtleSwgYXR0cmlidXRlVmFsdWVdID0gdHlwZS5yZXBsYWNlKC9cXFt8XFxdfFwiL2csICcnKS5zcGxpdCgnPScpXG4gICAgICAgIHZhbGlkYXRlID0gKG5vZGUpID0+IHtcbiAgICAgICAgICBjb25zdCBoYXNBdHRyaWJ1dGUgPSBPYmplY3Qua2V5cyhub2RlLmF0dHJpYnMpLmluZGV4T2YoYXR0cmlidXRlS2V5KSA+IC0xXG4gICAgICAgICAgaWYgKGhhc0F0dHJpYnV0ZSkgeyAvLyByZWdhcmQgb3B0aW9uYWwgYXR0cmlidXRlVmFsdWVcbiAgICAgICAgICAgIGlmICghYXR0cmlidXRlVmFsdWUgfHwgKG5vZGUuYXR0cmlic1thdHRyaWJ1dGVLZXldID09PSBhdHRyaWJ1dGVWYWx1ZSkpIHtcbiAgICAgICAgICAgICAgcmV0dXJuIHRydWVcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9XG4gICAgICAgICAgcmV0dXJuIGZhbHNlXG4gICAgICAgIH1cbiAgICAgICAgaW5zdHJ1Y3Rpb24gPSBmdW5jdGlvbiBjaGVja0F0dHJpYnV0ZSAobm9kZSwgcm9vdCkge1xuICAgICAgICAgIGlmIChkaXNjb3Zlcikge1xuICAgICAgICAgICAgY29uc3QgTm9kZUxpc3QgPSBbXVxuICAgICAgICAgICAgdHJhdmVyc2VEZXNjZW5kYW50cyhbbm9kZV0sIChkZXNjZW5kYW50KSA9PiB7XG4gICAgICAgICAgICAgIGlmICh2YWxpZGF0ZShkZXNjZW5kYW50KSkge1xuICAgICAgICAgICAgICAgIE5vZGVMaXN0LnB1c2goZGVzY2VuZGFudClcbiAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSlcbiAgICAgICAgICAgIHJldHVybiBOb2RlTGlzdFxuICAgICAgICAgIH1cbiAgICAgICAgICByZXR1cm4gKHR5cGVvZiBub2RlID09PSAnZnVuY3Rpb24nKSA/IG5vZGUodmFsaWRhdGUpIDogZ2V0QW5jZXN0b3Iobm9kZSwgcm9vdCwgdmFsaWRhdGUpXG4gICAgICAgIH1cbiAgICAgICAgYnJlYWtcblxuICAgICAgLy8gaWQ6ICcjJ1xuICAgICAgY2FzZSAvXiMvLnRlc3QodHlwZSk6XG4gICAgICAgIGNvbnN0IGlkID0gdHlwZS5zdWJzdHIoMSlcbiAgICAgICAgdmFsaWRhdGUgPSAobm9kZSkgPT4ge1xuICAgICAgICAgIHJldHVybiBub2RlLmF0dHJpYnMuaWQgPT09IGlkXG4gICAgICAgIH1cbiAgICAgICAgaW5zdHJ1Y3Rpb24gPSBmdW5jdGlvbiBjaGVja0lkIChub2RlLCByb290KSB7XG4gICAgICAgICAgaWYgKGRpc2NvdmVyKSB7XG4gICAgICAgICAgICBjb25zdCBOb2RlTGlzdCA9IFtdXG4gICAgICAgICAgICB0cmF2ZXJzZURlc2NlbmRhbnRzKFtub2RlXSwgKGRlc2NlbmRhbnQsIGRvbmUpID0+IHtcbiAgICAgICAgICAgICAgaWYgKHZhbGlkYXRlKGRlc2NlbmRhbnQpKSB7XG4gICAgICAgICAgICAgICAgTm9kZUxpc3QucHVzaChkZXNjZW5kYW50KVxuICAgICAgICAgICAgICAgIGRvbmUoKVxuICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KVxuICAgICAgICAgICAgcmV0dXJuIE5vZGVMaXN0XG4gICAgICAgICAgfVxuICAgICAgICAgIHJldHVybiAodHlwZW9mIG5vZGUgPT09ICdmdW5jdGlvbicpID8gbm9kZSh2YWxpZGF0ZSkgOiBnZXRBbmNlc3Rvcihub2RlLCByb290LCB2YWxpZGF0ZSlcbiAgICAgICAgfVxuICAgICAgICBicmVha1xuXG4gICAgICAvLyB1bml2ZXJzYWw6ICcqJ1xuICAgICAgY2FzZSAvXFwqLy50ZXN0KHR5cGUpOlxuICAgICAgICB2YWxpZGF0ZSA9IChub2RlKSA9PiB0cnVlXG4gICAgICAgIGluc3RydWN0aW9uID0gZnVuY3Rpb24gY2hlY2tVbml2ZXJzYWwgKG5vZGUsIHJvb3QpIHtcbiAgICAgICAgICBpZiAoZGlzY292ZXIpIHtcbiAgICAgICAgICAgIGNvbnN0IE5vZGVMaXN0ID0gW11cbiAgICAgICAgICAgIHRyYXZlcnNlRGVzY2VuZGFudHMoW25vZGVdLCAoZGVzY2VuZGFudCkgPT4gTm9kZUxpc3QucHVzaChkZXNjZW5kYW50KSlcbiAgICAgICAgICAgIHJldHVybiBOb2RlTGlzdFxuICAgICAgICAgIH1cbiAgICAgICAgICByZXR1cm4gKHR5cGVvZiBub2RlID09PSAnZnVuY3Rpb24nKSA/IG5vZGUodmFsaWRhdGUpIDogZ2V0QW5jZXN0b3Iobm9kZSwgcm9vdCwgdmFsaWRhdGUpXG4gICAgICAgIH1cbiAgICAgICAgYnJlYWtcblxuICAgICAgLy8gdGFnOiAnLi4uJ1xuICAgICAgZGVmYXVsdDpcbiAgICAgICAgdmFsaWRhdGUgPSAobm9kZSkgPT4ge1xuICAgICAgICAgIHJldHVybiBub2RlLm5hbWUgPT09IHR5cGVcbiAgICAgICAgfVxuICAgICAgICBpbnN0cnVjdGlvbiA9IGZ1bmN0aW9uIGNoZWNrVGFnIChub2RlLCByb290KSB7XG4gICAgICAgICAgaWYgKGRpc2NvdmVyKSB7XG4gICAgICAgICAgICBjb25zdCBOb2RlTGlzdCA9IFtdXG4gICAgICAgICAgICB0cmF2ZXJzZURlc2NlbmRhbnRzKFtub2RlXSwgKGRlc2NlbmRhbnQpID0+IHtcbiAgICAgICAgICAgICAgaWYgKHZhbGlkYXRlKGRlc2NlbmRhbnQpKSB7XG4gICAgICAgICAgICAgICAgTm9kZUxpc3QucHVzaChkZXNjZW5kYW50KVxuICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KVxuICAgICAgICAgICAgcmV0dXJuIE5vZGVMaXN0XG4gICAgICAgICAgfVxuICAgICAgICAgIHJldHVybiAodHlwZW9mIG5vZGUgPT09ICdmdW5jdGlvbicpID8gbm9kZSh2YWxpZGF0ZSkgOiBnZXRBbmNlc3Rvcihub2RlLCByb290LCB2YWxpZGF0ZSlcbiAgICAgICAgfVxuICAgIH1cblxuICAgIGlmICghcHNldWRvKSB7XG4gICAgICByZXR1cm4gaW5zdHJ1Y3Rpb25cbiAgICB9XG5cbiAgICBjb25zdCBydWxlID0gcHNldWRvLm1hdGNoKC8tKGNoaWxkfHR5cGUpXFwoKFxcZCspXFwpJC8pXG4gICAgY29uc3Qga2luZCA9IHJ1bGVbMV1cbiAgICBjb25zdCBpbmRleCA9IHBhcnNlSW50KHJ1bGVbMl0sIDEwKSAtIDFcblxuICAgIGNvbnN0IHZhbGlkYXRlUHNldWRvID0gKG5vZGUpID0+IHtcbiAgICAgIGlmIChub2RlKSB7XG4gICAgICAgIHZhciBjb21wYXJlU2V0ID0gbm9kZS5wYXJlbnQuY2hpbGRUYWdzXG4gICAgICAgIGlmIChraW5kID09PSAndHlwZScpIHtcbiAgICAgICAgICBjb21wYXJlU2V0ID0gY29tcGFyZVNldC5maWx0ZXIodmFsaWRhdGUpXG4gICAgICAgIH1cbiAgICAgICAgY29uc3Qgbm9kZUluZGV4ID0gY29tcGFyZVNldC5maW5kSW5kZXgoKGNoaWxkKSA9PiBjaGlsZCA9PT0gbm9kZSlcbiAgICAgICAgaWYgKG5vZGVJbmRleCA9PT0gaW5kZXgpIHtcbiAgICAgICAgICByZXR1cm4gdHJ1ZVxuICAgICAgICB9XG4gICAgICB9XG4gICAgICByZXR1cm4gZmFsc2VcbiAgICB9XG5cbiAgICByZXR1cm4gZnVuY3Rpb24gZW5oYW5jZUluc3RydWN0aW9uIChub2RlKSB7XG4gICAgICBjb25zdCBtYXRjaCA9IGluc3RydWN0aW9uKG5vZGUpXG4gICAgICBpZiAoZGlzY292ZXIpIHtcbiAgICAgICAgcmV0dXJuIG1hdGNoLnJlZHVjZSgoTm9kZUxpc3QsIG1hdGNoZWROb2RlKSA9PiB7XG4gICAgICAgICAgaWYgKHZhbGlkYXRlUHNldWRvKG1hdGNoZWROb2RlKSkge1xuICAgICAgICAgICAgTm9kZUxpc3QucHVzaChtYXRjaGVkTm9kZSlcbiAgICAgICAgICB9XG4gICAgICAgICAgcmV0dXJuIE5vZGVMaXN0XG4gICAgICAgIH0sIFtdKVxuICAgICAgfVxuICAgICAgcmV0dXJuIHZhbGlkYXRlUHNldWRvKG1hdGNoKSAmJiBtYXRjaFxuICAgIH1cbiAgfSlcbn1cblxuLyoqXG4gKiBXYWxraW5nIHJlY3Vyc2l2ZSB0byBpbnZva2UgY2FsbGJhY2tzXG4gKlxuICogQHBhcmFtIHtBcnJheS48SFRNTEVsZW1lbnQ+fSBub2RlcyAgIC0gW2Rlc2NyaXB0aW9uXVxuICogQHBhcmFtIHtGdW5jdGlvbn0gICAgICAgICAgICBoYW5kbGVyIC0gW2Rlc2NyaXB0aW9uXVxuICovXG5mdW5jdGlvbiB0cmF2ZXJzZURlc2NlbmRhbnRzIChub2RlcywgaGFuZGxlcikge1xuICBub2Rlcy5mb3JFYWNoKChub2RlKSA9PiB7XG4gICAgdmFyIHByb2dyZXNzID0gdHJ1ZVxuICAgIGhhbmRsZXIobm9kZSwgKCkgPT4gcHJvZ3Jlc3MgPSBmYWxzZSlcbiAgICBpZiAobm9kZS5jaGlsZFRhZ3MgJiYgcHJvZ3Jlc3MpIHtcbiAgICAgIHRyYXZlcnNlRGVzY2VuZGFudHMobm9kZS5jaGlsZFRhZ3MsIGhhbmRsZXIpXG4gICAgfVxuICB9KVxufVxuXG4vKipcbiAqIEJ1YmJsZSB1cCBmcm9tIGJvdHRvbSB0byB0b3BcbiAqXG4gKiBAcGFyYW0gIHtIVE1MRUxlbWVudH0gbm9kZSAgICAgLSBbZGVzY3JpcHRpb25dXG4gKiBAcGFyYW0gIHtIVE1MRUxlbWVudH0gcm9vdCAgICAgLSBbZGVzY3JpcHRpb25dXG4gKiBAcGFyYW0gIHtGdW5jdGlvbn0gICAgdmFsaWRhdGUgLSBbZGVzY3JpcHRpb25dXG4gKiBAcmV0dXJuIHtIVE1MRUxlbWVudH0gICAgICAgICAgLSBbZGVzY3JpcHRpb25dXG4gKi9cbmZ1bmN0aW9uIGdldEFuY2VzdG9yIChub2RlLCByb290LCB2YWxpZGF0ZSkge1xuICB3aGlsZSAobm9kZS5wYXJlbnQpIHtcbiAgICBub2RlID0gbm9kZS5wYXJlbnRcbiAgICBpZiAodmFsaWRhdGUobm9kZSkpIHtcbiAgICAgIHJldHVybiBub2RlXG4gICAgfVxuICAgIGlmIChub2RlID09PSByb290KSB7XG4gICAgICBicmVha1xuICAgIH1cbiAgfVxuICByZXR1cm4gbnVsbFxufVxuIl19
