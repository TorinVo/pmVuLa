let nl2br = function(str, replaceMode, isXhtml){
    var breakTag = (isXhtml) ? '<br />' : '<br>';
    var replaceStr = (replaceMode) ? '$1'+ breakTag : '$1'+ breakTag +'$2';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, replaceStr);
};
export { nl2br };

let br2nl = function(str, replaceMode){
    var replaceStr = (replaceMode) ? "\n" : '';
    return str.replace(/<\s*\/?br\s*[\/]?>/gi, replaceStr);
};
export { br2nl };