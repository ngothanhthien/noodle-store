import rules from '../mapping/rule';
export default function (form){
    if(form.name==""){
        throw "Tên không được để trống";
    }
    if(form.phone==""){
        throw "Số điện thoại không được để trống";
    }
    const arr=Object.keys(rules);
    for(var i=0; i<form.rules.length; i++){
        if(!arr.includes(form.rules[i])){
            throw "Quyền không hợp lệ";
        }
    }
}