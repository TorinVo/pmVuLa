export function apiFetchUser() {
    return axios.get('api/profile/roles')
        .then(response => response)
        .catch(error => error)
}