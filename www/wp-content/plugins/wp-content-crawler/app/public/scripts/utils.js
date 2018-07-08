/**
 * Format a string like sprintf function of PHP. Example usage:
 * "{0} is dead, but {1} is alive! {0} {2}".format("ASP", "ASP.NET")
 *
 * @see http://stackoverflow.com/a/4673436/2883487
 */
if (!String.prototype.format) {
    String.prototype.format = function() {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function(match, number) {
            return typeof args[number] != 'undefined'
                ? args[number]
                : match
                ;
        });
    };
}