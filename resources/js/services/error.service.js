// import SecureLS from 'secure-ls';

export const errorService = {
    handleError,
};

function handleError(error) {
    //Setup Error Message
    let message = null
    if (typeof error !== 'undefined') {
        if (error.hasOwnProperty('message')) {
            message = error.message
        }
    }

    if (typeof error.response !== 'undefined') {

        //Setup Generic Response Messages
        if (error.response.status === 500) {
            message = 'Internal Server Error'
        } else if (error.response.status === 401) {
            if (localStorage.getItem('user')) {
                localStorage.removeItem('user')
            }
            // if(SecureLS.get('user')){
            //     SecureLS.set('permission', response.data)
            // }
            if (error.response.data.hasOwnProperty('error'))
                message = error.response.data.error
            else if (error.response.data.hasOwnProperty('message'))
                message = error.response.data.message

            setTimeout(function () {
                location.reload(true)
            }, 1000)

        } else if (error.response.status === 422) {
            //Validation Message
        }

        //Try to Use the Response Message
        if (error.hasOwnProperty('response') && error.response.hasOwnProperty('data')) {
            if (error.response.data.hasOwnProperty('message') && error.response.data.message.length > 0) {
                message = error.response.data.message
            }
        }
    }

    return message
}