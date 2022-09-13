const SEPARATOR='.';
const UNIT='đ';
export const numberToMoney=(number)=>number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, SEPARATOR)+UNIT;