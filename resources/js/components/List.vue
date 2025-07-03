<template>
    <div class="log-wrapper">
        <!-- Header and Buttons -->
        <div class="header">
            <h2>Request Logs</h2>
            <div class="buttons">
                <button @click="fetchLogs">🔄 Refresh</button>
                <button v-for="n in 3" :key="n" @click="triggerRequest(n)">Request {{ n }}</button>
                <button @click="clearLogs">🗑️ Clear Logs</button>
            </div>
        </div>

        <!-- Logs Table -->
        <table v-if="logs.data?.length">
            <thead>
            <tr>
                <th>ID</th>
                <th>Method</th>
                <th>IP</th>
                <th>Response Time (sec)</th>
                <th>Created</th>
                <th>URL</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="log in filteredLogs" :key="log.id">
                <td>{{ log.id }}</td>
                <td>{{ log.method }}</td>
                <td>{{ log.ip }}</td>
                <td>{{ log.response_time.toFixed(2) }}</td>
                <td>{{ log.created_at }}</td>
                <td>{{ log.url }}</td>
            </tr>
            </tbody>
        </table>

        <p v-else>No logs found.</p>

        <!-- Pagination -->
        <div class="pagination" v-if="logs.last_page > 1">
            <button @click="changePage(logs.current_page - 1)" :disabled="logs.current_page === 1">⬅ Prev</button>
            <span>Page {{ logs.current_page }} of {{ logs.last_page }}</span>
            <button @click="changePage(logs.current_page + 1)" :disabled="logs.current_page === logs.last_page">Next ➡</button>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            logs: { data: [] },
            methodFilter: null,
            currentPage: 1
        };
    },
    computed: {
        filteredLogs() {
            return this.methodFilter
                ? this.logs.data.filter(log => log.method === this.methodFilter)
                : this.logs.data;
        }
    },
    methods: {
        async fetchLogs(page = 1) {
            try {
                const res = await fetch(`http://localhost:8000/logs?page=${page}`);
                this.logs = await res.json();
                this.currentPage = page;
            } catch (err) {
                console.error('Failed to fetch logs:', err);
            }
        },
        async changePage(page) {
            if (page >= 1 && page <= this.logs.last_page) this.fetchLogs(page);
        },
        async triggerRequest(n) {
            try {
                await fetch(`http://localhost:8000/request/${n}`);
                this.fetchLogs(this.currentPage);
            } catch (err) {
                console.error(`Request ${n} failed`, err);
            }
        },
        async clearLogs() {
            if (confirm('Are you sure you want to delete all logs?')) {
                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                if (!tokenMeta) {
                    alert('CSRF token missing in HTML.');
                    return;
                }

                const token = tokenMeta.getAttribute('content');

                const res = await fetch('/logs/clear', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                });

                if (res.ok) {
                    this.fetchLogs(1);
                } else {
                    console.error('Clear failed:', res.status);
                    alert('Failed to clear logs.');
                }
            }
        }
    },
    mounted() {
        this.fetchLogs();
    }
};
</script>

<style scoped>
.log-wrapper {
    padding: 20px;
}

.header {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 15px;
}

.buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
th, td {
    padding: 10px;
    border: 1px solid #ccc;
}
button {
    padding: 8px 12px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
button:hover {
    background-color: #0056b3;
}
.pagination {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}
</style>
