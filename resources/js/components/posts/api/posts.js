export function apiFetchPostOpen() {
    return axios.get('/api/ticketopen')
        .then(response => response)
        .catch(error => error)
}
export function apiFetchMyPost() {
    return axios.get('/api/myticket')
        .then(response => response)
        .catch(error => error)
}
