export function addOrRemove(array, value) {
    var index = array.indexOf(value);

    if (index === -1) {
        array.push(value);
    } else {
        array.splice(index, 1);
    }
}
export function removeObjFromArrayById(array,id){
    array.splice(array.findIndex(function(i){
        return i.id ===id;
    }), 1);
}
export function arrayHasSubArray(array,subArray){
    return subArray.every((element) => array.includes(element));
}