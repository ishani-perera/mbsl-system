/**
 * MBSL Insurance - API Client
 * Handles all fetch() requests with safe JSON parsing and error handling.
 */

class MBSLApi {
    constructor(baseUrl = '') {
        // Use relative path for better cross-browser compatibility
        this.baseUrl = '';
        this.authEndpoint = './api/controllers/Auth.php';
        this.policyEndpoint = './api/controllers/Policy.php';
    }

    getUrl(endpoint, params = {}) {
        // Filter out empty string values to avoid PHP interpretation issues
        const filtered = Object.entries(params)
            .filter(([key, val]) => val !== '' && val !== null && val !== undefined)
            .reduce((obj, [key, val]) => {obj[key] = val; return obj;}, {});
        const qs = new URLSearchParams(filtered).toString();
        return `${endpoint}${qs ? `?${qs}` : ''}`;
    }

    async request(endpoint, options = {}, params = {}) {
        const url = this.getUrl(endpoint, params);

        try {
            const response = await fetch(url, {
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    ...((options.headers) || {})
                },
                ...options
            });

            const text = await response.text();
            console.log(`[API] ${options.method || 'GET'} ${url}`, { status: response.status, responseText: text.substring(0, 200) });

            let data;
            try {
                // Clean response text
                const cleanText = text.trim();
                if (!cleanText) {
                    throw new Error('Empty response');
                }
                const jsonStart = cleanText.indexOf('{');
                const jsonEnd = cleanText.lastIndexOf('}');
                if (jsonStart === -1 || jsonEnd === -1) {
                    throw new Error('No JSON object found in response');
                }
                const jsonText = cleanText.substring(jsonStart, jsonEnd + 1);
                data = JSON.parse(jsonText);
            } catch (e) {
                console.error('Invalid JSON response:', text, e);
                return {
                    ok: false,
                    status: response.status,
                    data: {
                        success: false,
                        message: 'Invalid server response: ' + e.message
                    }
                };
            }

            return {
                ok: response.ok,
                status: response.status,
                data
            };

        } catch (error) {
            console.error('Fetch error:', error);

            return {
                ok: false,
                status: 0,
                data: {
                    success: false,
                    message: 'Network error: ' + error.message
                }
            };
        }
    }

    // ── Auth Methods ─────────────────────────────────────────

    async login(email, password) {
        return this.request(this.authEndpoint, {
            method: 'POST',
            body: JSON.stringify({ email, password })
        }, {
            action: 'login'
        });
    }

    async logout() {
        return this.request(this.authEndpoint, {
            method: 'POST'
        }, {
            action: 'logout'
        });
    }

    async checkAuth() {
        return this.request(this.authEndpoint, {}, {
            action: 'check'
        });
    }

    // ── Policy Methods ───────────────────────────────────────

    async listPolicies(params = {}) {
        return this.request(this.policyEndpoint, {}, {
            action: 'list',
            ...params
        });
    }

    async getPolicy(id) {
        return this.request(this.policyEndpoint, {}, {
            action: 'get',
            id
        });
    }

    async createPolicy(formData) {
        return this.request(this.policyEndpoint, {
            method: 'POST',
            body: formData
        }, {
            action: 'create'
        });
    }

    async createPolicyJson(policyData) {
        return this.request(this.policyEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(policyData)
        }, {
            action: 'create'
        });
    }

    async updatePolicy(id, formData) {
        return this.request(this.policyEndpoint, {
            method: 'POST',
            body: formData
        }, {
            action: 'update',
            id
        });
    }

    async deletePolicy(id) {
        return this.request(this.policyEndpoint, {
            method: 'DELETE'
        }, {
            action: 'delete',
            id
        });
    }

    async reorderImages(order) {
        return this.request(this.policyEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order })
        }, {
            action: 'reorder'
        });
    }

    async deleteImage(imgId) {
        return this.request(this.policyEndpoint, {
            method: 'DELETE'
        }, {
            action: 'delimg',
            img_id: imgId
        });
    }

    async stats() {
        return this.request(this.policyEndpoint, {}, {
            action: 'stats'
        });
    }
}

const Api = new MBSLApi();
export default Api;