<template>
    <div class="log-wrapper">
        <!-- Header and Buttons -->
        <div class="header">
            <h2>Request Logger</h2>

            <div class="buttons-row">
                <div class="buttons">
                    <button @click="fetchLogs" :disabled="loading">🔄 Refresh</button>
                    <button v-for="n in 3" :key="n" @click="triggerRequest(n)" :disabled="loading">
                        Request {{ n }}
                    </button>
                    <button @click="clearLogs" :disabled="loading">🗑️ Clear Logs</button>
                </div>

                <!-- Static container prevents layout shift -->
                <div class="loading-placeholder">
                    <p :class="{ visible: loading }" class="loading-message-inline">⏳ Loading...</p>
                </div>
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
            <button @click="changePage(logs.current_page - 1)" :disabled="logs.current_page === 1 || loading">
                ⬅ Prev
            </button>
            <div class="page-info">Page {{ logs.current_page }} of {{ logs.last_page }}</div>
            <button @click="changePage(logs.current_page + 1)" :disabled="logs.current_page === logs.last_page || loading">
                Next ➡
            </button>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            logs: { data: [] },
            methodFilter: null,
            currentPage: 1,
            loading: false,
        };
    },
    computed: {
        filteredLogs() {
            return this.methodFilter
                ? this.logs.data.filter((log) => log.method === this.methodFilter)
                : this.logs.data;
        },
    },
    methods: {
        async fetchLogs(page = 1) {
            this.loading = true;
            try {
                const res = await fetch(`http://localhost:8000/logs?page=${page}`);
                this.logs = await res.json();
                this.currentPage = page;
            } catch (err) {
                console.error("Failed to fetch logs:", err);
            } finally {
                this.loading = false;
            }
        },
        async changePage(page) {
            if (page >= 1 && page <= this.logs.last_page) {
                await this.fetchLogs(page);
            }
        },
        async triggerRequest(n) {
            this.loading = true;
            try {
                await fetch(`http://localhost:8000/request/${n}`);
                await this.fetchLogs(this.currentPage);
            } catch (err) {
                console.error(`Request ${n} failed`, err);
            } finally {
                this.loading = false;
            }
        },
        async clearLogs() {
            if (!confirm("Are you sure you want to delete all logs?")) return;

            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            if (!tokenMeta) {
                alert("CSRF token missing in HTML.");
                return;
            }

            const token = tokenMeta.getAttribute("content");

            this.loading = true;
            try {
                const res = await fetch("/logs/clear", {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": token,
                        Accept: "application/json",
                    },
                });

                if (res.ok) {
                    await this.fetchLogs(1);
                } else {
                    console.error("Clear failed:", res.status);
                    alert("Failed to clear logs.");
                }
            } catch (err) {
                console.error("Clear failed:", err);
            } finally {
                this.loading = false;
            }
        },
    },
    mounted() {
        this.fetchLogs();
    },
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

.buttons-row {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.loading-placeholder {
    width: 150px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: flex-start;
}

.loading-message-inline {
    font-weight: bold;
    color: #333;
    background-color: #f8f9fa;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    text-align: center;
    visibility: hidden;
}

.loading-message-inline.visible {
    visibility: visible;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th,
td {
    padding: 10px;
    border: 1px solid #ccc;
    text-align: center;
}

button {
    padding: 8px 12px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:disabled {
    background-color: #6c757d;
    cursor: not-allowed;
}

button:hover:not(:disabled) {
    background-color: #0056b3;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    margin-top: 20px;
}

.page-info {
    font-weight: bold;
}

</style>
