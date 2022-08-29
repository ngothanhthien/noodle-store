import router from '../router';
import Cookies from 'js-cookie'
export const backToLogin=()=>{
    Cookies.remove('User Token');
    Cookies.remove('User Info');
    router.push({name:'login'});
}