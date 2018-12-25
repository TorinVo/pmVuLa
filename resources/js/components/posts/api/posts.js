export function apiFetchPostOpen() {
    return axios.get('/api/ticketopen')
        .then(response => response)
}
export function apiFetchMyPost() {
    return axios.get('/api/myticket')
        .then(response => response)
}
