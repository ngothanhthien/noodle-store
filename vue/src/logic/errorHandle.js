import { backToLogin } from "./auth";
const UNAUTHORIZED=401;
const FORBIDDEN=403;
export default function(code,error){
    if(!code){
        console.log(error);
    }
    if(code==UNAUTHORIZED||code==FORBIDDEN){
        backToLogin();
    }
}