'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = match;

var _utilities = require('./utilities');

var defaultIgnore = {
  attribute: function attribute(attributeName) {
    return ['style', 'data-reactid', 'data-react-checksum'].indexOf(attributeName) > -1;
  }
};

/**
 * Get the path of the element
 *
 * @param  {HTMLElement} node    - [description]
 * @param  {Object}      options - [description]
 * @return {string}              - [description]
 */
/**
 * # Match
 *
 * Retrieve selector for a node.
 */

function match(node, options) {
  var _options$root = options.root,
      root = _options$root === undefined ? document : _options$root,
      _options$skip = options.skip,
      skip = _options$skip === undefined ? null : _options$skip,
      _options$priority = options.priority,
      priority = _options$priority === undefined ? ['id', 'class', 'href', 'src'] : _options$priority,
      _options$ignore = options.ignore,
      ignore = _options$ignore === undefined ? {} : _options$ignore;


  var path = [];
  var element = node;
  var length = path.length;
  var ignoreClass = false;

  var skipCompare = skip && (Array.isArray(skip) ? skip : [skip]).map(function (entry) {
    if (typeof entry !== 'function') {
      return function (element) {
        return element === entry;
      };
    }
    return entry;
  });

  var skipChecks = function skipChecks(element) {
    return skip && skipCompare.some(function (compare) {
      return compare(element);
    });
  };

  Object.keys(ignore).forEach(function (type) {
    if (type === 'class') {
      ignoreClass = true;
    }
    var predicate = ignore[type];
    if (typeof predicate === 'function') return;
    if (typeof predicate === 'number') {
      predicate = predicate.toString();
    }
    if (typeof predicate === 'string') {
      predicate = new RegExp((0, _utilities.escapeValue)(predicate).replace(/\\/g, '\\\\'));
    }
    if (typeof predicate === 'boolean') {
      predicate = predicate ? /(?:)/ : /.^/;
    }
    // check class-/attributename for regex
    ignore[type] = function (name, value) {
      return predicate.test(value);
    };
  });

  if (ignoreClass) {
    (function () {
      var ignoreAttribute = ignore.attribute;
      ignore.attribute = function (name, value, defaultPredicate) {
        return ignore.class(value) || ignoreAttribute && ignoreAttribute(name, value, defaultPredicate);
      };
    })();
  }

  while (element !== root) {
    if (skipChecks(element) !== true) {
      // ~ global
      if (checkAttributes(priority, element, ignore, path, root)) break;
      if (checkTag(element, ignore, path, root)) break;

      // ~ local
      checkAttributes(priority, element, ignore, path);
      if (path.length === length) {
        checkTag(element, ignore, path);
      }

      // define only one part each iteration
      if (path.length === length) {
        checkChilds(priority, element, ignore, path);
      }
    }

    element = element.parentNode;
    length = path.length;
  }

  if (element === root) {
    var pattern = findPattern(priority, element, ignore);
    path.unshift(pattern);
  }

  return path.join(' ');
}

/**
 * Extend path with attribute identifier
 *
 * @param  {Array.<string>} priority - [description]
 * @param  {HTMLElement}    element  - [description]
 * @param  {Object}         ignore   - [description]
 * @param  {Array.<string>} path     - [description]
 * @param  {HTMLElement}    parent   - [description]
 * @return {boolean}                 - [description]
 */
function checkAttributes(priority, element, ignore, path) {
  var parent = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : element.parentNode;

  var pattern = findAttributesPattern(priority, element, ignore);
  if (pattern) {
    var matches = parent.querySelectorAll(pattern);
    if (matches.length === 1) {
      path.unshift(pattern);
      return true;
    }
  }
  return false;
}

/**
 * Lookup attribute identifier
 *
 * @param  {Array.<string>} priority - [description]
 * @param  {HTMLElement}    element  - [description]
 * @param  {Object}         ignore   - [description]
 * @return {string?}                 - [description]
 */
function findAttributesPattern(priority, element, ignore) {
  var attributes = element.attributes;
  var sortedKeys = Object.keys(attributes).sort(function (curr, next) {
    var currPos = priority.indexOf(attributes[curr].name);
    var nextPos = priority.indexOf(attributes[next].name);
    if (nextPos === -1) {
      if (currPos === -1) {
        return 0;
      }
      return -1;
    }
    return currPos - nextPos;
  });

  for (var i = 0, l = sortedKeys.length; i < l; i++) {
    var key = sortedKeys[i];
    var attribute = attributes[key];
    var attributeName = attribute.name;
    var attributeValue = (0, _utilities.escapeValue)(attribute.value);

    var currentIgnore = ignore[attributeName] || ignore.attribute;
    var currentDefaultIgnore = defaultIgnore[attributeName] || defaultIgnore.attribute;
    if (checkIgnore(currentIgnore, attributeName, attributeValue, currentDefaultIgnore)) {
      continue;
    }

    var pattern = '[' + attributeName + '="' + attributeValue + '"]';

    if (/\b\d/.test(attributeValue) === false) {
      if (attributeName === 'id') {
        pattern = '#' + attributeValue;
      }

      if (attributeName === 'class') {
        var className = attributeValue.trim().replace(/\s+/g, '.');
        pattern = '.' + className;
      }
    }

    return pattern;
  }
  return null;
}

/**
 * Extend path with tag identifier
 *
 * @param  {HTMLElement}    element - [description]
 * @param  {Object}         ignore  - [description]
 * @param  {Array.<string>} path    - [description]
 * @param  {HTMLElement}    parent  - [description]
 * @return {boolean}                - [description]
 */
function checkTag(element, ignore, path) {
  var parent = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : element.parentNode;

  var pattern = findTagPattern(element, ignore);
  if (pattern) {
    var matches = parent.getElementsByTagName(pattern);
    if (matches.length === 1) {
      path.unshift(pattern);
      return true;
    }
  }
  return false;
}

/**
 * Lookup tag identifier
 *
 * @param  {HTMLElement} element - [description]
 * @param  {Object}      ignore  - [description]
 * @return {boolean}             - [description]
 */
function findTagPattern(element, ignore) {
  var tagName = element.tagName.toLowerCase();
  if (checkIgnore(ignore.tag, null, tagName)) {
    return null;
  }
  return tagName;
}

/**
 * Extend path with specific child identifier
 *
 * NOTE: 'childTags' is a custom property to use as a view filter for tags using 'adapter.js'
 *
 * @param  {Array.<string>} priority - [description]
 * @param  {HTMLElement}    element  - [description]
 * @param  {Object}         ignore   - [description]
 * @param  {Array.<string>} path     - [description]
 * @return {boolean}                 - [description]
 */
function checkChilds(priority, element, ignore, path) {
  var parent = element.parentNode;
  var children = parent.childTags || parent.children;
  for (var i = 0, l = children.length; i < l; i++) {
    var child = children[i];
    if (child === element) {
      var childPattern = findPattern(priority, child, ignore);
      if (!childPattern) {
        return console.warn('\n          Element couldn\'t be matched through strict ignore pattern!\n        ', child, ignore, childPattern);
      }
      var pattern = '> ' + childPattern + ':nth-child(' + (i + 1) + ')';
      path.unshift(pattern);
      return true;
    }
  }
  return false;
}

/**
 * Lookup identifier
 *
 * @param  {Array.<string>} priority - [description]
 * @param  {HTMLElement}    element  - [description]
 * @param  {Object}         ignore   - [description]
 * @return {string}                  - [description]
 */
function findPattern(priority, element, ignore) {
  var pattern = findAttributesPattern(priority, element, ignore);
  if (!pattern) {
    pattern = findTagPattern(element, ignore);
  }
  return pattern;
}

/**
 * Validate with custom and default functions
 *
 * @param  {Function} predicate        - [description]
 * @param  {string?}  name             - [description]
 * @param  {string}   value            - [description]
 * @param  {Function} defaultPredicate - [description]
 * @return {boolean}                   - [description]
 */
function checkIgnore(predicate, name, value, defaultPredicate) {
  if (!value) {
    return true;
  }
  var check = predicate || defaultPredicate;
  if (!check) {
    return false;
  }
  return check(name, value, defaultPredicate);
}
module.exports = exports['default'];
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm1hdGNoLmpzIl0sIm5hbWVzIjpbIm1hdGNoIiwiZGVmYXVsdElnbm9yZSIsImF0dHJpYnV0ZSIsImF0dHJpYnV0ZU5hbWUiLCJpbmRleE9mIiwibm9kZSIsIm9wdGlvbnMiLCJyb290IiwiZG9jdW1lbnQiLCJza2lwIiwicHJpb3JpdHkiLCJpZ25vcmUiLCJwYXRoIiwiZWxlbWVudCIsImxlbmd0aCIsImlnbm9yZUNsYXNzIiwic2tpcENvbXBhcmUiLCJBcnJheSIsImlzQXJyYXkiLCJtYXAiLCJlbnRyeSIsInNraXBDaGVja3MiLCJzb21lIiwiY29tcGFyZSIsIk9iamVjdCIsImtleXMiLCJmb3JFYWNoIiwidHlwZSIsInByZWRpY2F0ZSIsInRvU3RyaW5nIiwiUmVnRXhwIiwicmVwbGFjZSIsIm5hbWUiLCJ2YWx1ZSIsInRlc3QiLCJpZ25vcmVBdHRyaWJ1dGUiLCJkZWZhdWx0UHJlZGljYXRlIiwiY2xhc3MiLCJjaGVja0F0dHJpYnV0ZXMiLCJjaGVja1RhZyIsImNoZWNrQ2hpbGRzIiwicGFyZW50Tm9kZSIsInBhdHRlcm4iLCJmaW5kUGF0dGVybiIsInVuc2hpZnQiLCJqb2luIiwicGFyZW50IiwiZmluZEF0dHJpYnV0ZXNQYXR0ZXJuIiwibWF0Y2hlcyIsInF1ZXJ5U2VsZWN0b3JBbGwiLCJhdHRyaWJ1dGVzIiwic29ydGVkS2V5cyIsInNvcnQiLCJjdXJyIiwibmV4dCIsImN1cnJQb3MiLCJuZXh0UG9zIiwiaSIsImwiLCJrZXkiLCJhdHRyaWJ1dGVWYWx1ZSIsImN1cnJlbnRJZ25vcmUiLCJjdXJyZW50RGVmYXVsdElnbm9yZSIsImNoZWNrSWdub3JlIiwiY2xhc3NOYW1lIiwidHJpbSIsImZpbmRUYWdQYXR0ZXJuIiwiZ2V0RWxlbWVudHNCeVRhZ05hbWUiLCJ0YWdOYW1lIiwidG9Mb3dlckNhc2UiLCJ0YWciLCJjaGlsZHJlbiIsImNoaWxkVGFncyIsImNoaWxkIiwiY2hpbGRQYXR0ZXJuIiwiY29uc29sZSIsIndhcm4iLCJjaGVjayJdLCJt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S2dDLE9BQUwsQ0FBYUYsT0FBYjtBQUNBLGFBQU8sSUFBUDtBQUNEO0FBQ0Y7QUFDRCxTQUFPLEtBQVA7QUFDRDs7QUFFRDs7Ozs7Ozs7QUFRQSxTQUFTQyxXQUFULENBQXNCakMsUUFBdEIsRUFBZ0NHLE9BQWhDLEVBQXlDRixNQUF6QyxFQUFpRDtBQUMvQyxNQUFJK0IsVUFBVUssc0JBQXNCckMsUUFBdEIsRUFBZ0NHLE9BQWhDLEVBQXlDRixNQUF6QyxDQUFkO0FBQ0EsTUFBSSxDQUFDK0IsT0FBTCxFQUFjO0FBQ1pBLGNBQVV3QixlQUFlckQsT0FBZixFQUF3QkYsTUFBeEIsQ0FBVjtBQUNEO0FBQ0QsU0FBTytCLE9BQVA7QUFDRDs7QUFFRDs7Ozs7Ozs7O0FBU0EsU0FBU3FCLFdBQVQsQ0FBc0JuQyxTQUF0QixFQUFpQ0ksSUFBakMsRUFBdUNDLEtBQXZDLEVBQThDRyxnQkFBOUMsRUFBZ0U7QUFDOUQsTUFBSSxDQUFDSCxLQUFMLEVBQVk7QUFDVixXQUFPLElBQVA7QUFDRDtBQUNELE1BQU00QyxRQUFRakQsYUFBYVEsZ0JBQTNCO0FBQ0EsTUFBSSxDQUFDeUMsS0FBTCxFQUFZO0FBQ1YsV0FBTyxLQUFQO0FBQ0Q7QUFDRCxTQUFPQSxNQUFNN0MsSUFBTixFQUFZQyxLQUFaLEVBQW1CRyxnQkFBbkIsQ0FBUDtBQUNEIiwiZmlsZSI6Im1hdGNoLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyoqXG4gKiAjIE1hdGNoXG4gKlxuICogUmV0cmlldmUgc2VsZWN0b3IgZm9yIGEgbm9kZS5cbiAqL1xuXG5pbXBvcnQgeyBlc2NhcGVWYWx1ZSB9IGZyb20gJy4vdXRpbGl0aWVzJ1xuXG5jb25zdCBkZWZhdWx0SWdub3JlID0ge1xuICBhdHRyaWJ1dGUgKGF0dHJpYnV0ZU5hbWUpIHtcbiAgICByZXR1cm4gW1xuICAgICAgJ3N0eWxlJyxcbiAgICAgICdkYXRhLXJlYWN0aWQnLFxuICAgICAgJ2RhdGEtcmVhY3QtY2hlY2tzdW0nXG4gICAgXS5pbmRleE9mKGF0dHJpYnV0ZU5hbWUpID4gLTFcbiAgfVxufVxuXG4vKipcbiAqIEdldCB0aGUgcGF0aCBvZiB0aGUgZWxlbWVudFxuICpcbiAqIEBwYXJhbSAge0hUTUxFbGVtZW50fSBub2RlICAgIC0gW2Rlc2NyaXB0aW9uXVxuICogQHBhcmFtICB7T2JqZWN0fSAgICAgIG9wdGlvbnMgLSBbZGVzY3JpcHRpb25dXG4gKiBAcmV0dXJuIHtzdHJpbmd9ICAgICAgICAgICAgICAtIFtkZXNjcmlwdGlvbl1cbiAqL1xuZXhwb3J0IGRlZmF1bHQgZnVuY3Rpb24gbWF0Y2ggKG5vZGUsIG9wdGlvbnMpIHtcblxuICBjb25zdCB7XG4gICAgcm9vdCA9IGRvY3VtZW50LFxuICAgIHNraXAgPSBudWxsLFxuICAgIHByaW9yaXR5ID0gWydpZCcsICdjbGFzcycsICdocmVmJywgJ3NyYyddLFxuICAgIGlnbm9yZSA9IHt9XG4gIH0gPSBvcHRpb25zXG5cbiAgY29uc3QgcGF0aCA9IFtdXG4gIHZhciBlbGVtZW50ID0gbm9kZVxuICB2YXIgbGVuZ3RoID0gcGF0aC5sZW5ndGhcbiAgdmFyIGlnbm9yZUNsYXNzID0gZmFsc2VcblxuICBjb25zdCBza2lwQ29tcGFyZSA9IHNraXAgJiYgKEFycmF5LmlzQXJyYXkoc2tpcCkgPyBza2lwIDogW3NraXBdKS5tYXAoKGVudHJ5KSA9PiB7XG4gICAgaWYgKHR5cGVvZiBlbnRyeSAhPT0gJ2Z1bmN0aW9uJykge1xuICAgICAgcmV0dXJuIChlbGVtZW50KSA9PiBlbGVtZW50ID09PSBlbnRyeVxuICAgIH1cbiAgICByZXR1cm4gZW50cnlcbiAgfSlcblxuICBjb25zdCBza2lwQ2hlY2tzID0gKGVsZW1lbnQpID0+IHtcbiAgICByZXR1cm4gc2tpcCAmJiBza2lwQ29tcGFyZS5zb21lKChjb21wYXJlKSA9PiBjb21wYXJlKGVsZW1lbnQpKVxuICB9XG5cbiAgT2JqZWN0LmtleXMoaWdub3JlKS5mb3JFYWNoKCh0eXBlKSA9PiB7XG4gICAgaWYgKHR5cGUgPT09ICdjbGFzcycpIHtcbiAgICAgIGlnbm9yZUNsYXNzID0gdHJ1ZVxuICAgIH1cbiAgICB2YXIgcHJlZGljYXRlID0gaWdub3JlW3R5cGVdXG4gICAgaWYgKHR5cGVvZiBwcmVkaWNhdGUgPT09ICdmdW5jdGlvbicpIHJldHVyblxuICAgIGlmICh0eXBlb2YgcHJlZGljYXRlID09PSAnbnVtYmVyJykge1xuICAgICAgcHJlZGljYXRlID0gcHJlZGljYXRlLnRvU3RyaW5nKClcbiAgICB9XG4gICAgaWYgKHR5cGVvZiBwcmVkaWNhdGUgPT09ICdzdHJpbmcnKSB7XG4gICAgICBwcmVkaWNhdGUgPSBuZXcgUmVnRXhwKGVzY2FwZVZhbHVlKHByZWRpY2F0ZSkucmVwbGFjZSgvXFxcXC9nLCAnXFxcXFxcXFwnKSlcbiAgICB9XG4gICAgaWYgKHR5cGVvZiBwcmVkaWNhdGUgPT09ICdib29sZWFuJykge1xuICAgICAgcHJlZGljYXRlID0gcHJlZGljYXRlID8gLyg/OikvIDogLy5eL1xuICAgIH1cbiAgICAvLyBjaGVjayBjbGFzcy0vYXR0cmlidXRlbmFtZSBmb3IgcmVnZXhcbiAgICBpZ25vcmVbdHlwZV0gPSAobmFtZSwgdmFsdWUpID0+IHByZWRpY2F0ZS50ZXN0KHZhbHVlKVxuICB9KVxuXG4gIGlmIChpZ25vcmVDbGFzcykge1xuICAgIGNvbnN0IGlnbm9yZUF0dHJpYnV0ZSA9IGlnbm9yZS5hdHRyaWJ1dGVcbiAgICBpZ25vcmUuYXR0cmlidXRlID0gKG5hbWUsIHZhbHVlLCBkZWZhdWx0UHJlZGljYXRlKSA9PiB7XG4gICAgICByZXR1cm4gaWdub3JlLmNsYXNzKHZhbHVlKSB8fCBpZ25vcmVBdHRyaWJ1dGUgJiYgaWdub3JlQXR0cmlidXRlKG5hbWUsIHZhbHVlLCBkZWZhdWx0UHJlZGljYXRlKVxuICAgIH1cbiAgfVxuXG4gIHdoaWxlIChlbGVtZW50ICE9PSByb290KSB7XG4gICAgaWYgKHNraXBDaGVja3MoZWxlbWVudCkgIT09IHRydWUpIHtcbiAgICAgIC8vIH4gZ2xvYmFsXG4gICAgICBpZiAoY2hlY2tBdHRyaWJ1dGVzKHByaW9yaXR5LCBlbGVtZW50LCBpZ25vcmUsIHBhdGgsIHJvb3QpKSBicmVha1xuICAgICAgaWYgKGNoZWNrVGFnKGVsZW1lbnQsIGlnbm9yZSwgcGF0aCwgcm9vdCkpIGJyZWFrXG5cbiAgICAgIC8vIH4gbG9jYWxcbiAgICAgIGNoZWNrQXR0cmlidXRlcyhwcmlvcml0eSwgZWxlbWVudCwgaWdub3JlLCBwYXRoKVxuICAgICAgaWYgKHBhdGgubGVuZ3RoID09PSBsZW5ndGgpIHtcbiAgICAgICAgY2hlY2tUYWcoZWxlbWVudCwgaWdub3JlLCBwYXRoKVxuICAgICAgfVxuXG4gICAgICAvLyBkZWZpbmUgb25seSBvbmUgcGFydCBlYWNoIGl0ZXJhdGlvblxuICAgICAgaWYgKHBhdGgubGVuZ3RoID09PSBsZW5ndGgpIHtcbiAgICAgICAgY2hlY2tDaGlsZHMocHJpb3JpdHksIGVsZW1lbnQsIGlnbm9yZSwgcGF0aClcbiAgICAgIH1cbiAgICB9XG5cbiAgICBlbGVtZW50ID0gZWxlbWVudC5wYXJlbnROb2RlXG4gICAgbGVuZ3RoID0gcGF0aC5sZW5ndGhcbiAgfVxuXG4gIGlmIChlbGVtZW50ID09PSByb290KSB7XG4gICAgY29uc3QgcGF0dGVybiA9IGZpbmRQYXR0ZXJuKHByaW9yaXR5LCBlbGVtZW50LCBpZ25vcmUpXG4gICAgcGF0aC51bnNoaWZ0KHBhdHRlcm4pXG4gIH1cblxuICByZXR1cm4gcGF0aC5qb2luKCcgJylcbn1cblxuLyoqXG4gKiBFeHRlbmQgcGF0aCB3aXRoIGF0dHJpYnV0ZSBpZGVudGlmaWVyXG4gKlxuICogQHBhcmFtICB7QXJyYXkuPHN0cmluZz59IHByaW9yaXR5IC0gW2Rlc2NyaXB0aW9uXVxuICogQHBhcmFtICB7SFRNTEVsZW1lbnR9ICAgIGVsZW1lbnQgIC0gW2Rlc2NyaXB0aW9uXVxuICogQHBhcmFtICB7T2JqZWN0fSAgICAgICAgIGlnbm9yZSAgIC0gW2Rlc2NyaXB0aW9uXVxuICogQHBhcmFtICB7QXJyYXkuPHN0cmluZz59IHBhdGggICAgIC0gW2Rlc2NyaXB0aW9uXVxuICogQHBhcmFtICB7SFRNTEVsZW1lbnR9ICAgIHBhcmVudCAgIC0gW2Rlc2NyaXB0aW9uXVxuICogQHJldHVybiB7Ym9vbGVhbn0gICAgICAgICAgICAgICAgIC0gW2Rlc2NyaXB0aW9uXVxuICovXG5mdW5jdGlvbiBjaGVja0F0dHJpYnV0ZXMgKHByaW9yaXR5LCBlbGVtZW50LCBpZ25vcmUsIHBhdGgsIHBhcmVudCA9IGVsZW1lbnQucGFyZW50Tm9kZSkge1xuICBjb25zdCBwYXR0ZXJuID0gZmluZEF0dHJpYnV0ZXNQYXR0ZXJuKHByaW9yaXR5LCBlbGVtZW50LCBpZ25vcmUpXG4gIGlmIChwYXR0ZXJuKSB7XG4gICAgY29uc3QgbWF0Y2hlcyA9IHBhcmVudC5xdWVyeVNlbGVjdG9yQWxsKHBhdHRlcm4pXG4gICAgaWYgKG1hdGNoZXMubGVuZ3RoID09PSAxKSB7XG4gICAgICBwYXRoLnVuc2hpZnQocGF0dGVybilcbiAgICAgIHJldHVybiB0cnVlXG4gICAgfVxuICB9XG4gIHJldHVybiBmYWxzZVxufVxuXG4vKipcbiAqIExvb2t1cCBhdHRyaWJ1dGUgaWRlbnRpZmllclxuICpcbiAqIEBwYXJhbSAge0FycmF5LjxzdHJpbmc+fSBwcmlvcml0eSAtIFtkZXNjcmlwdGlvbl1cbiAqIEBwYXJhbSAge0hUTUxFbGVtZW50fSAgICBlbGVtZW50ICAtIFtkZXNjcmlwdGlvbl1cbiAqIEBwYXJhbSAge09iamVjdH0gICAgICAgICBpZ25vcmUgICAtIFtkZXNjcmlwdGlvbl1cbiAqIEByZXR1cm4ge3N0cmluZz99ICAgICAgICAgICAgICAgICAtIFtkZXNjcmlwdGlvbl1cbiAqL1xuZnVuY3Rpb24gZmluZEF0dHJpYnV0ZXNQYXR0ZXJuIChwcmlvcml0eSwgZWxlbWVudCwgaWdub3JlKSB7XG4gIGNvbnN0IGF0dHJpYnV0ZXMgPSBlbGVtZW50LmF0dHJpYnV0ZXNcbiAgY29uc3Qgc29ydGVkS2V5cyA9IE9iamVjdC5rZXlzKGF0dHJpYnV0ZXMpLnNvcnQoKGN1cnIsIG5leHQpID0+IHtcbiAgICBjb25zdCBjdXJyUG9zID0gcHJpb3JpdHkuaW5kZXhPZihhdHRyaWJ1dGVzW2N1cnJdLm5hbWUpXG4gICAgY29uc3QgbmV4dFBvcyA9IHByaW9yaXR5LmluZGV4T2YoYXR0cmlidXRlc1tuZXh0XS5uYW1lKVxuICAgIGlmIChuZXh0UG9zID09PSAtMSkge1xuICAgICAgaWYgKGN1cnJQb3MgPT09IC0xKSB7XG4gICAgICAgIHJldHVybiAwXG4gICAgICB9XG4gICAgICByZXR1cm4gLTFcbiAgICB9XG4gICAgcmV0dXJuIGN1cnJQb3MgLSBuZXh0UG9zXG4gIH0pXG5cbiAgZm9yICh2YXIgaSA9IDAsIGwgPSBzb3J0ZWRLZXlzLmxlbmd0aDsgaSA8IGw7IGkrKykge1xuICAgIGNvbnN0IGtleSA9IHNvcnRlZEtleXNbaV1cbiAgICBjb25zdCBhdHRyaWJ1dGUgPSBhdHRyaWJ1dGVzW2tleV1cbiAgICBjb25zdCBhdHRyaWJ1dGVOYW1lID0gYXR0cmlidXRlLm5hbWVcbiAgICBjb25zdCBhdHRyaWJ1dGVWYWx1ZSA9IGVzY2FwZVZhbHVlKGF0dHJpYnV0ZS52YWx1ZSlcblxuICAgIGNvbnN0IGN1cnJlbnRJZ25vcmUgPSBpZ25vcmVbYXR0cmlidXRlTmFtZV0gfHwgaWdub3JlLmF0dHJpYnV0ZVxuICAgIGNvbnN0IGN1cnJlbnREZWZhdWx0SWdub3JlID0gZGVmYXVsdElnbm9yZVthdHRyaWJ1dGVOYW1lXSB8fCBkZWZhdWx0SWdub3JlLmF0dHJpYnV0ZVxuICAgIGlmIChjaGVja0lnbm9yZShjdXJyZW50SWdub3JlLCBhdHRyaWJ1dGVOYW1lLCBhdHRyaWJ1dGVWYWx1ZSwgY3VycmVudERlZmF1bHRJZ25vcmUpKSB7XG4gICAgICBjb250aW51ZVxuICAgIH1cblxuICAgIHZhciBwYXR0ZXJuID0gYFske2F0dHJpYnV0ZU5hbWV9PVwiJHthdHRyaWJ1dGVWYWx1ZX1cIl1gXG5cbiAgICBpZiAoKC9cXGJcXGQvKS50ZXN0KGF0dHJpYnV0ZVZhbHVlKSA9PT0gZmFsc2UpIHtcbiAgICAgIGlmIChhdHRyaWJ1dGVOYW1lID09PSAnaWQnKSB7XG4gICAgICAgIHBhdHRlcm4gPSBgIyR7YXR0cmlidXRlVmFsdWV9YFxuICAgICAgfVxuXG4gICAgICBpZiAoYXR0cmlidXRlTmFtZSA9PT0gJ2NsYXNzJykge1xuICAgICAgICBjb25zdCBjbGFzc05hbWUgPSBhdHRyaWJ1dGVWYWx1ZS50cmltKCkucmVwbGFjZSgvXFxzKy9nLCAnLicpXG4gICAgICAgIHBhdHRlcm4gPSBgLiR7Y2xhc3NOYW1lfWBcbiAgICAgIH1cbiAgICB9XG5cbiAgICByZXR1cm4gcGF0dGVyblxuICB9XG4gIHJldHVybiBudWxsXG59XG5cbi8qKlxuICogRXh0ZW5kIHBhdGggd2l0aCB0YWcgaWRlbnRpZmllclxuICpcbiAqIEBwYXJhbSAge0hUTUxFbGVtZW50fSAgICBlbGVtZW50IC0gW2Rlc2NyaXB0aW9uXVxuICogQHBhcmFtICB7T2JqZWN0fSAgICAgICAgIGlnbm9yZSAgLSBbZGVzY3JpcHRpb25dXG4gKiBAcGFyYW0gIHtBcnJheS48c3RyaW5nPn0gcGF0aCAgICAtIFtkZXNjcmlwdGlvbl1cbiAqIEBwYXJhbSAge0hUTUxFbGVtZW50fSAgICBwYXJlbnQgIC0gW2Rlc2NyaXB0aW9uXVxuICogQHJldHVybiB7Ym9vbGVhbn0gICAgICAgICAgICAgICAgLSBbZGVzY3JpcHRpb25dXG4gKi9cbmZ1bmN0aW9uIGNoZWNrVGFnIChlbGVtZW50LCBpZ25vcmUsIHBhdGgsIHBhcmVudCA9IGVsZW1lbnQucGFyZW50Tm9kZSkge1xuICBjb25zdCBwYXR0ZXJuID0gZmluZFRhZ1BhdHRlcm4oZWxlbWVudCwgaWdub3JlKVxuICBpZiAocGF0dGVybikge1xuICAgIGNvbnN0IG1hdGNoZXMgPSBwYXJlbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUocGF0dGVybilcbiAgICBpZiAobWF0Y2hlcy5sZW5ndGggPT09IDEpIHtcbiAgICAgIHBhdGgudW5zaGlmdChwYXR0ZXJuKVxuICAgICAgcmV0dXJuIHRydWVcbiAgICB9XG4gIH1cbiAgcmV0dXJuIGZhbHNlXG59XG5cbi8qKlxuICogTG9va3VwIHRhZyBpZGVudGlmaWVyXG4gKlxuICogQHBhcmFtICB7SFRNTEVsZW1lbnR9IGVsZW1lbnQgLSBbZGVzY3JpcHRpb25dXG4gKiBAcGFyYW0gIHtPYmplY3R9ICAgICAgaWdub3JlICAtIFtkZXNjcmlwdGlvbl1cbiAqIEByZXR1cm4ge2Jvb2xlYW59ICAgICAgICAgICAgIC0gW2Rlc2NyaXB0aW9uXVxuICovXG5mdW5jdGlvbiBmaW5kVGFnUGF0dGVybiAoZWxlbWVudCwgaWdub3JlKSB7XG4gIGNvbnN0IHRhZ05hbWUgPSBlbGVtZW50LnRhZ05hbWUudG9Mb3dlckNhc2UoKVxuICBpZiAoY2hlY2tJZ25vcmUoaWdub3JlLnRhZywgbnVsbCwgdGFnTmFtZSkpIHtcbiAgICByZXR1cm4gbnVsbFxuICB9XG4gIHJldHVybiB0YWdOYW1lXG59XG5cbi8qKlxuICogRXh0ZW5kIHBhdGggd2l0aCBzcGVjaWZpYyBjaGlsZCBpZGVudGlmaWVyXG4gKlxuICogTk9URTogJ2NoaWxkVGFncycgaXMgYSBjdXN0b20gcHJvcGVydHkgdG8gdXNlIGFzIGEgdmlldyBmaWx0ZXIgZm9yIHRhZ3MgdXNpbmcgJ2FkYXB0ZXIuanMnXG4gKlxuICogQHBhcmFtICB7QXJyYXkuPHN0cmluZz59IHByaW9yaXR5IC0gW2Rlc2NyaXB0aW9uXVxuICogQHBhcmFtICB7SFRNTEVsZW1lbnR9ICAgIGVsZW1lbnQgIC0gW2Rlc2NyaXB0aW9uXVxuICogQHBhcmFtICB7T2JqZWN0fSAgICAgICAgIGlnbm9yZSAgIC0gW2Rlc2NyaXB0aW9uXVxuICogQHBhcmFtICB7QXJyYXkuPHN0cmluZz59IHBhdGggICAgIC0gW2Rlc2NyaXB0aW9uXVxuICogQHJldHVybiB7Ym9vbGVhbn0gICAgICAgICAgICAgICAgIC0gW2Rlc2NyaXB0aW9uXVxuICovXG5mdW5jdGlvbiBjaGVja0NoaWxkcyAocHJpb3JpdHksIGVsZW1lbnQsIGlnbm9yZSwgcGF0aCkge1xuICBjb25zdCBwYXJlbnQgPSBlbGVtZW50LnBhcmVudE5vZGVcbiAgY29uc3QgY2hpbGRyZW4gPSBwYXJlbnQuY2hpbGRUYWdzIHx8IHBhcmVudC5jaGlsZHJlblxuICBmb3IgKHZhciBpID0gMCwgbCA9IGNoaWxkcmVuLmxlbmd0aDsgaSA8IGw7IGkrKykge1xuICAgIGNvbnN0IGNoaWxkID0gY2hpbGRyZW5baV1cbiAgICBpZiAoY2hpbGQgPT09IGVsZW1lbnQpIHtcbiAgICAgIGNvbnN0IGNoaWxkUGF0dGVybiA9IGZpbmRQYXR0ZXJuKHByaW9yaXR5LCBjaGlsZCwgaWdub3JlKVxuICAgICAgaWYgKCFjaGlsZFBhdHRlcm4pIHtcbiAgICAgICAgcmV0dXJuIGNvbnNvbGUud2FybihgXG4gICAgICAgICAgRWxlbWVudCBjb3VsZG5cXCd0IGJlIG1hdGNoZWQgdGhyb3VnaCBzdHJpY3QgaWdub3JlIHBhdHRlcm4hXG4gICAgICAgIGAsIGNoaWxkLCBpZ25vcmUsIGNoaWxkUGF0dGVybilcbiAgICAgIH1cbiAgICAgIGNvbnN0IHBhdHRlcm4gPSBgPiAke2NoaWxkUGF0dGVybn06bnRoLWNoaWxkKCR7aSsxfSlgXG4gICAgICBwYXRoLnVuc2hpZnQocGF0dGVybilcbiAgICAgIHJldHVybiB0cnVlXG4gICAgfVxuICB9XG4gIHJldHVybiBmYWxzZVxufVxuXG4vKipcbiAqIExvb2t1cCBpZGVudGlmaWVyXG4gKlxuICogQHBhcmFtICB7QXJyYXkuPHN0cmluZz59IHByaW9yaXR5IC0gW2Rlc2NyaXB0aW9uXVxuICogQHBhcmFtICB7SFRNTEVsZW1lbnR9ICAgIGVsZW1lbnQgIC0gW2Rlc2NyaXB0aW9uXVxuICogQHBhcmFtICB7T2JqZWN0fSAgICAgICAgIGlnbm9yZSAgIC0gW2Rlc2NyaXB0aW9uXVxuICogQHJldHVybiB7c3RyaW5nfSAgICAgICAgICAgICAgICAgIC0gW2Rlc2NyaXB0aW9uXVxuICovXG5mdW5jdGlvbiBmaW5kUGF0dGVybiAocHJpb3JpdHksIGVsZW1lbnQsIGlnbm9yZSkge1xuICB2YXIgcGF0dGVybiA9IGZpbmRBdHRyaWJ1dGVzUGF0dGVybihwcmlvcml0eSwgZWxlbWVudCwgaWdub3JlKVxuICBpZiAoIXBhdHRlcm4pIHtcbiAgICBwYXR0ZXJuID0gZmluZFRhZ1BhdHRlcm4oZWxlbWVudCwgaWdub3JlKVxuICB9XG4gIHJldHVybiBwYXR0ZXJuXG59XG5cbi8qKlxuICogVmFsaWRhdGUgd2l0aCBjdXN0b20gYW5kIGRlZmF1bHQgZnVuY3Rpb25zXG4gKlxuICogQHBhcmFtICB7RnVuY3Rpb259IHByZWRpY2F0ZSAgICAgICAgLSBbZGVzY3JpcHRpb25dXG4gKiBAcGFyYW0gIHtzdHJpbmc/fSAgbmFtZSAgICAgICAgICAgICAtIFtkZXNjcmlwdGlvbl1cbiAqIEBwYXJhbSAge3N0cmluZ30gICB2YWx1ZSAgICAgICAgICAgIC0gW2Rlc2NyaXB0aW9uXVxuICogQHBhcmFtICB7RnVuY3Rpb259IGRlZmF1bHRQcmVkaWNhdGUgLSBbZGVzY3JpcHRpb25dXG4gKiBAcmV0dXJuIHtib29sZWFufSAgICAgICAgICAgICAgICAgICAtIFtkZXNjcmlwdGlvbl1cbiAqL1xuZnVuY3Rpb24gY2hlY2tJZ25vcmUgKHByZWRpY2F0ZSwgbmFtZSwgdmFsdWUsIGRlZmF1bHRQcmVkaWNhdGUpIHtcbiAgaWYgKCF2YWx1ZSkge1xuICAgIHJldHVybiB0cnVlXG4gIH1cbiAgY29uc3QgY2hlY2sgPSBwcmVkaWNhdGUgfHwgZGVmYXVsdFByZWRpY2F0ZVxuICBpZiAoIWNoZWNrKSB7XG4gICAgcmV0dXJuIGZhbHNlXG4gIH1cbiAgcmV0dXJuIGNoZWNrKG5hbWUsIHZhbHVlLCBkZWZhdWx0UHJlZGljYXRlKVxufVxuIl19
