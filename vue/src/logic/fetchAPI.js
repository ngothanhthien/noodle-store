const headers = {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
};
export const fetchGet=async (api,token='')=>{
    const response=await fetch(api,{
        headers: {
            'Authorization': 'Bearer ' + token,
            ...headers
        }
    });
    return response.json();
}
export const fetchGetWithParams=async (api,params,token='')=>{
    const response=fetch(api+'?'+ new URLSearchParams(params),{
        headers: {
            'Authorization': 'Bearer ' + token,
            ...headers
        }
    });
    return response.json();
}
export const fetchMethod=async (api,method,params,token='')=>{
    const response=await fetch(api,{
        method: method,
        body: JSON.stringify(params),
        headers: {
            'Authorization': 'Bearer ' + token,
            ...headers
        }
    });
    return response.json();
}