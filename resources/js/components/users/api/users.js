export function apiFetchUser() {
    return axios.get('/api/user', {
        params: {
            type: 'all'
        }
    })
    .then(response => response)
    .catch(error => error)
}