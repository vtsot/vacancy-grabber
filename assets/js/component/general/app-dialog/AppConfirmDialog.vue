<template>
    <v-dialog
        v-model="dialog"
        :width="props.width"
        :scrollable="props.scrollable"
        :opacity="props.opacity"
        :persistent="persistent"
        @keydown.esc="onCanceled"
    >
        <v-card
            :prepend-icon="mdiAlertCircleOutline"
            :max-width="props.maxWidth"
            :min-width="props.minWidth"
            :max-height="props.maxHeight"
            :min-height="props.minHeight"
        >
            <template #title>
                <slot v-if="$slots.title || confirmTitle" name="title">
                    {{ confirmTitle }}
                </slot>
            </template>

            <template #subtitle>
                <slot v-if="$slots.subtitle || props.subtitle" name="subtitle">
                    {{ props.subtitle }}
                </slot>
            </template>

            <template #text>
                <slot v-if="$slots.text || confirmText" name="text">
                    {{ confirmText }}
                </slot>
            </template>

            <template #actions>
                <slot v-if="$slots.actions || props.actions" name="actions">
                    <div class="mb-2 d-flex ga-4">
                        <v-btn
                            :text="props.btnCancelLabel"
                            :prepend-icon="mdiCancel"
                            :loading="loading"
                            color="secondary"
                            variant="tonal"
                            class="px-4"
                            @click="onCanceled()"
                        />
                        <v-btn
                            :text="confirmLabel"
                            :prepend-icon="confirmIcon"
                            :color="confirmColor"
                            :loading="loading"
                            variant="tonal"
                            class="px-4"
                            @click="onConfirmed"
                        />
                    </div>
                </slot>
            </template>
        </v-card>
    </v-dialog>
</template>

<script setup>
    import {computed} from 'vue'
    import { mdiAlertCircleOutline, mdiCancel, mdiCheckCircleOutline, mdiDelete } from '@mdi/js';

    const props = defineProps({
        persistent: {type: Boolean, default: true},
        delete: {type: Boolean, default: false},
        scrollable: {type: Boolean, default: true},
        leaveOpen: {type: Boolean, default: false},
        loading: {type: Boolean, default: false},
        actions: {type: Boolean, default: true},

        width: {
            type: [String, Number, null],
            default: 'auto',
        },

        minWidth: {
            type: [String, Number, null],
            default: 420,
        },

        maxWidth: {
            type: [String, Number, null],
            default: 500,
        },

        maxHeight: {
            type: [String, Number, null],
            default: 500,
        },

        minHeight: {
            type: [String, Number, null],
            default: 100,
        },

        opacity: {
            type: [Number, null],
            default: 0.5,
        },

        title: {
            type: [String, null],
            default: null,
        },

        titleDelete: {
            type: [String, null],
            default: 'Confirm deletion',
        },

        titleConfirm: {
            type: [String, null],
            default: 'Confirm action',
        },

        subtitle: {
            type: [String, null],
        },

        text: {
            type: [String, null],
            default: null,
        },

        textConfirm: {type: [String, null], default: 'Are you sure you want to continue the current action?',},
        textDelete: {type: [String, null], default: 'Are you sure you want to delete?',},

        btnDeleteLabel: {
            type: [String, null],
            default: 'Delete',
        },

        btnConfirmLabel: {
            type: [String],
            default: 'Confirm',
        },

        btnCancelLabel: {
            type: [String],
            default: 'Cancel',
        },

    });

    const confirmTitle = computed(() => props.delete ? props.titleDelete : props.titleConfirm);
    const confirmText = computed(() => props.delete ? props.textDelete : props.textConfirm);
    const confirmLabel = computed(() => props.delete ? props.btnDeleteLabel : props.btnConfirmLabel);
    const confirmIcon = computed(() => props.delete ? mdiDelete : mdiCheckCircleOutline);
    const confirmColor = computed(() => props.delete ? 'error' : 'primary');

    const emit = defineEmits(['confirmed', 'canceled'])
    const dialog = defineModel('dialog', { type: Boolean, default: false });

    const onConfirmed = () => {
        emit('confirmed');
        if (!props.leaveOpen) {
            dialog.value = false;
        }
    };

    const onCanceled = () => {
        emit('canceled');
        if (!props.leaveOpen) {
            dialog.value = false;
        }
    };
</script>
