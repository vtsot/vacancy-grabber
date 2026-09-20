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
                <slot v-if="$slots.title || props.title" name="title">
                    {{ props.title }}
                </slot>
            </template>

            <template #subtitle>
                <slot v-if="$slots.subtitle || props.subtitle" name="subtitle">
                    {{ props.subtitle }}
                </slot>
            </template>

            <template #text>
                <slot v-if="$slots.text || props.text" name="text">
                    {{ props.text }}
                </slot>
            </template>

            <template #actions>
                <slot name="actions">
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
        isDelete: {type: Boolean, default: false},
        scrollable: {type: Boolean, default: true},
        leaveOpen: {type: Boolean, default: false},
        loading: {type: Boolean, default: false},

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
            default: 'Confirm action',
        },

        subtitle: {
            type: [String, null],
        },

        text: {
            type: [String, null],
            default: 'Are you sure you want to continue the current action?',
        },

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

    const confirmLabel = computed(() => props.isDelete ? props.btnDeleteLabel : props.btnConfirmLabel);
    const confirmIcon = computed(() => props.isDelete ? mdiDelete : mdiCheckCircleOutline);
    const confirmColor = computed(() => props.isDelete ? 'error' : 'primary');

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
