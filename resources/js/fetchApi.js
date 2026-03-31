const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

export async function fetchApi(url, options = {}) {
    const response = await fetch(url, {
        ...options,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...options.headers,
        },
        body: options.body ? JSON.stringify(options.body) : undefined,
    });

    if (!response.ok) {
        const error = new Error(`Request failed with status ${response.status}`);
        error.response = await response.json().catch(() => null);
        throw error;
    }

    return response.json();
}
