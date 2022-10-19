import config from '../config'
import {getUserToken} from '../logic/auth'
import axios from 'axios';
const server=config['apiServer'];
const token=getUserToken();
export async function getToppingByMealAPI(id){
    const response=await axios({
        url:server+'/meal/'+id+'/toppings',
        method:'GET',
        headers:{
            'Authorization': 'Bearer ' + token
        },
    });
    return response.data.map(topping=>topping.id);
}
export async function getAllToppingAPI(){
    const response= await axios({
        method: 'GET',
        url: server+"/meals/topping",
        headers: {
            'Authorization': 'Bearer ' + token
        },
    });
    return response.data;
}
export async function createMealAPI(formData){
    return axios({
        method: 'POST',
        url: server+"/meal",
        data: formData,
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'Multipart/form-data'
        }
    });
}
export async function getAllMealAPI(){
    return axios({
        method: 'GET',
        url: server+"/meals",
        headers: {
            'Authorization': 'Bearer ' + token
        },
    });
}
export async function updateMealAPI(formData,id){
    return axios({
        method: 'POST',
        url:server+"/meal/" + id,
        data: formData,
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'Multipart/form-data'
        }
    });
}
export async function deleteMealAPI(id){
    return axios({
        method:'DELETE',
        url:mealAPI+'/'+id,
        headers:{
            'Authorization': 'Bearer '+token,
        }
    })
}