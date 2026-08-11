<!-- src/views/admin/MessagesContact.vue -->

<script setup>
import { ref, onMounted } from 'vue';
import apiClient from '@/api/axios';

const messages = ref([]);
const isLoading = ref(true);
const error = ref('');
const filtreLu = ref('');

const selectedMessage = ref(null);
const showModal = ref(false);

async function fetchMessages() {
    isLoading.value = true;
    error.value = '';

    try {
        const params = {};
        if (filtreLu.value !== '') {
            params.lu = filtreLu.value;
        }

        const response = await apiClient.get('/admin/messages-contact', { params });
        messages.value = response.data.data || [];
    } catch (err) {
        error.value = 'Impossible de charger les messages.';
    } finally {
        isLoading.value = false;
    }
}

async function openMessage(message) {
    try {
        const response = await apiClient.get(`/admin/messages-contact/${message.id}`);
        selectedMessage.value = response.data;
        showModal.value = true;

        if (!message.lu) {
            message.lu = true;
        }
    } catch (err) {
        alert('Erreur lors du chargement du message.');
    }
}

function closeModal() {
    showModal.value = false;
    selectedMessage.value = null;
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function truncateText(text, maxLength = 80) {
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength).trim() + '...';
}

onMounted(() => {
    fetchMessages();
});
</script>

<template>
    <div class="messages-page">
        <div class="page-top">
            <h1>Messages reçus</h1>
            <div class="page-actions">
                <select v-model="filtreLu" class="filter-select" @change="fetchMessages">
                    <option value="">Tous</option>
                    <option value="0">Non lus</option>
                    <option value="1">Lus</option>
                </select>
            </div>
        </div>

        <div v-if="isLoading" class="loading">Chargement...</div>
        <div v-else-if="error" class="error">{{ error }}</div>
        <div v-else-if="messages.length === 0" class="empty">Aucun message.</div>

        <div v-else class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Sujet</th>
                        <th>Aperçu</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="message in messages"
                        :key="message.id"
                        class="message-row"
                        :class="{ 'message-row--unread': !message.lu }"
                        @click="openMessage(message)"
                    >
                        <td>
                            <span v-if="!message.lu" class="unread-dot" title="Non lu"></span>
                        </td>
                        <td class="cell-bold">{{ message.nom }}</td>
                        <td>{{ message.email }}</td>
                        <td>{{ message.sujet || '-' }}</td>
                        <td class="cell-preview">{{ truncateText(message.message) }}</td>
                        <td>{{ formatDate(message.created_at) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal détail message -->
        <Teleport to="body">
            <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
                <div class="modal">
                    <div class="modal-header">
                        <h3>Message</h3>
                        <button class="modal-close" @click="closeModal" aria-label="Fermer">✕</button>
                    </div>

                    <div v-if="selectedMessage" class="modal-body">
                        <div class="detail-row">
                            <span class="detail-label">De</span>
                            <span class="detail-value">{{ selectedMessage.nom }} ({{ selectedMessage.email }})</span>
                        </div>
                        <div class="detail-row" v-if="selectedMessage.sujet">
                            <span class="detail-label">Sujet</span>
                            <span class="detail-value">{{ selectedMessage.sujet }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Date</span>
                            <span class="detail-value">{{ formatDate(selectedMessage.created_at) }}</span>
                        </div>
                        <div class="detail-message">
                            <p>{{ selectedMessage.message }}</p>
                        </div>
                    </div>

                    <div class="modal-actions">
                        <button class="btn-close" @click="closeModal">Fermer</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;

.messages-page {
    max-width: 1200px;
}

.page-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: $spacing-lg;
    flex-wrap: wrap;
    gap: $spacing-md;

    h1 {
        font-size: $font-size-2xl;
        font-weight: $font-weight-bold;
        color: $color-text;
    }
}

.page-actions {
    display: flex;
    gap: $spacing-sm;
}

.filter-select {
    padding: $spacing-sm $spacing-md;
    border: 1px solid $color-border;
    border-radius: $radius-md;
    font-family: $font-family-base;
    font-size: $font-size-sm;
    color: $color-text;
    background-color: $color-white;
    cursor: pointer;
    outline: none;

    &:focus {
        border-color: $color-secondary;
    }
}

.loading,
.error,
.empty {
    text-align: center;
    padding: $spacing-2xl;
    font-size: $font-size-lg;
}

.error {
    color: $color-danger;
}

.empty {
    color: $color-text-light;
}

.table-wrapper {
    background-color: $color-white;
    border-radius: $radius-lg;
    box-shadow: $shadow-sm;
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: $font-size-sm;

    thead {
        background-color: $color-bg;
        border-bottom: 2px solid $color-border;

        th {
            padding: $spacing-md;
            text-align: left;
            font-weight: $font-weight-semi-bold;
            color: $color-text;
            white-space: nowrap;
        }
    }

    tbody {
        tr {
            border-bottom: 1px solid $color-border;
            cursor: pointer;
            transition: background-color $transition-fast;

            &:hover {
                background-color: lighten($color-bg, 2%);
            }

            &:last-child {
                border-bottom: none;
            }
        }

        td {
            padding: $spacing-md;
            vertical-align: middle;
        }
    }
}

.message-row--unread {
    background-color: lighten($color-secondary, 45%);
    font-weight: $font-weight-medium;

    &:hover {
        background-color: lighten($color-secondary, 40%);
    }
}

.unread-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: $color-secondary;
}

.cell-bold {
    font-weight: $font-weight-medium;
}

.cell-preview {
    max-width: 250px;
    color: $color-text-light;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

// =============================================
// MODAL
// =============================================
.modal-overlay {
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 200;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: $spacing-md;
}

.modal {
    background-color: $color-white;
    border-radius: $radius-lg;
    width: 100%;
    max-width: 600px;
    max-height: 85vh;
    overflow-y: auto;
    box-shadow: $shadow-lg;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: $spacing-lg;
    border-bottom: 1px solid $color-border;

    h3 {
        font-size: $font-size-lg;
        font-weight: $font-weight-semi-bold;
    }
}

.modal-close {
    background: none;
    border: none;
    font-size: $font-size-xl;
    cursor: pointer;
    color: $color-text-light;
    padding: $spacing-xs;

    &:hover {
        color: $color-danger;
    }
}

.modal-body {
    padding: $spacing-lg;
}

.detail-row {
    display: flex;
    gap: $spacing-md;
    margin-bottom: $spacing-sm;

    &:last-of-type {
        margin-bottom: $spacing-md;
    }
}

.detail-label {
    font-weight: $font-weight-semi-bold;
    color: $color-text;
    min-width: 60px;
    font-size: $font-size-sm;
}

.detail-value {
    color: $color-text-light;
    font-size: $font-size-sm;
}

.detail-message {
    background-color: $color-bg;
    padding: $spacing-md;
    border-radius: $radius-md;
    margin-top: $spacing-md;

    p {
        white-space: pre-wrap;
        line-height: 1.6;
        font-size: $font-size-sm;
    }
}

.modal-actions {
    padding: $spacing-md $spacing-lg;
    border-top: 1px solid $color-border;
    display: flex;
    justify-content: flex-end;
}

.btn-close {
    background-color: $color-bg;
    color: $color-text;
    border: 1px solid $color-border;
    padding: $spacing-sm $spacing-lg;
    border-radius: $radius-md;
    font-size: $font-size-sm;
    cursor: pointer;

    &:hover {
        background-color: darken($color-bg, 5%);
    }
}

@media (max-width: $breakpoint-mobile) {
    .page-top {
        flex-direction: column;
        align-items: stretch;
    }

    .data-table {
        font-size: 0.8rem;

        thead th,
        tbody td {
            padding: $spacing-sm;
        }
    }

    .cell-preview {
        max-width: 120px;
    }

    .modal {
        max-height: 90vh;
    }
}
</style>