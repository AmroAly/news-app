import axios from 'axios'
import Auth from '../store/auth'

export function get(url, responseType) {
    return axios({
        method: 'GET',
        url,
        responseType,
        headers: {
            'Authorization': `Bearer ${Auth.state.api_token}`
        }
    })
}

export function post(url, data) {
    return axios({
        method: 'POST',
        url,
        data,
        headers: {
            'Authorization': `Bearer ${Auth.state.api_token}`
        }
    })
}

export function del(url) {
    return axios({
        method: 'DELETE',
        url,
        headers: {
            'Authorization': `Bearer ${Auth.state.api_token}`
        }
    })
}
