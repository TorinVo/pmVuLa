// axios.interceptors.response.use(
//     function(response) {
//         console.log(response.status);
//         //swal('Updated!', 'Information has been updated.', 'success');
//         return response;
//     }, 
//     function(error) {
//         // handle error
//         if (error.response) {
//             if (error.response.status === 401) {
//                 window.location.href = '/login';
//                 return;
//             }
//             swal(error.response.data.message, 'error');
//             console.log(error.response);
//             console.log(error.response.data.message);
//         }
//     });

/**
 * Turn on gloabl error handling for specific call
 * axios.get('/user/1', {errorHandle: false}).then((response) => {
 *  */


import router from './router.js';
function errorResponseHandler(error) {
    // check for errorHandle config
    if( error.config.hasOwnProperty('errorHandle') && error.config.errorHandle === false ) {
        return Promise.reject(error);
    }

    // if has response show the error 
    if (error.response) {
        if (error.response.status === 401) {
            window.location.href = '/login';
            return;
        }else if(error.response.status === 422){
            return Promise.reject(error);
        }else if(error.response.status === 404){
            router.push({name: '404'})
            return;
        }
        swal({
            type: 'error',
            title: 'Oops...',
            showCancelButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            text: error.response.data.message
        }).then((result) => {
            if (result.value) {
                window.location.reload();
            }
        })
    }
}

// apply interceptor on response
axios.interceptors.response.use(
   response => response,
   errorResponseHandler
);

export default errorResponseHandler;