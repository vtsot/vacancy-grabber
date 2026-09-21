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
                <slot v-if="$slots.title || title" name="title">
                    {{ title }}
                </slot>
            </template>

            <template #subtitle>
                <slot v-if="$slots.subtitle || subtitle" name="subtitle">
                    {{ subtitle }}
                </slot>
            </template>

            <template #text>
                <slot v-if="$slots.text || text" name="text">
                    {{ text }}
                </slot>
            </template>

            <template #actions>

                <slot v-if="$slots.actions || props.actions" name="actions">
                    <div class="mb-2 d-flex ga-4">
                        <v-btn
                            v-if="btnCancelActive"
                            :text="btnCancelLabel"
                            :prepend-icon="btnCancelIcon"
                            :loading="loading"
                            :color="btnCancelColor"
                            @click="onCanceled()"
                            class="px-4"
                            variant="tonal"
                        />
                        <v-btn
                            v-if="btnConfirmActive"
                            :text="btnConfirmLabel"
                            :prepend-icon="btnConfirmIcon"
                            :color="btnConfirmColor"
                            :loading="loading"
                            @click="onConfirmed"
                            class="px-4"
                            variant="tonal"
                        />
                    </div>
                </slot>
            </template>
        </v-card>
    </v-dialog>
</template>

<script setup>
    import {computed} from 'vue'
    import {mdiAlertCircleOutline, mdiCancel, mdiCheckCircleOutline, mdiDelete} from '@mdi/js';

    const props = defineProps({
        persistent: {type: Boolean, default: true},
        delete: {type: Boolean, default: false},
        scrollable: {type: Boolean, default: true},
        leaveOpen: {type: Boolean, default: false},
        loading: {type: Boolean, default: false},
        actions: {type: Boolean, default: true},

        width: {type: [String, Number, null], default: 'auto'},
        minWidth: {type: [String, Number, null], default: 420},
        maxWidth: {type: [String, Number, null], default: 500},
        maxHeight: {type: [String, Number, null], default: 500},
        minHeight: {type: [String, Number, null], default: 100},

        title: {type: [String, null], default: null},
        subtitle: {type: [String, null]},

        titleDelete: {type: [String, null], default: 'Confirm deletion'},
        titleConfirm: {type: [String, null], default: 'Confirm action'},

        text: {type: [String, null], default: null},
        textConfirm: {type: [String, null], default: 'Are you sure you want to continue the current action?',},
        textDelete: {type: [String, null], default: 'Are you sure you want to delete?',},

        opacity: {type: [Number, null], default: 0.5},

        btnCancelActive: {type: Boolean, default: true},
        btnCancelColor: {type: [String], default: 'secondary'},
        btnCancelLabel: {type: [String], default: 'Cancel'},

        btnConfirmActive: {type: Boolean, default: true},
        btnConfirmColor: {type: [String], default: 'primary'},
        btnConfirmLabel: {type: [String], default: 'Confirm'},

        btnDeleteColor: {type: [String], default: 'error'},
        btnDeleteLabel: {type: [String], default: 'Delete'},
    });

    const title = computed(() => props.title ? props.title : (props.delete ? props.titleDelete : props.titleConfirm));
    const subtitle = computed(() => props.subtitle ? props.subtitle : null);
    const text = computed(() => props.text ? props.text : (props.delete ? props.textDelete : props.textConfirm));


    const btnCancelLabel = computed(() => props.btnCancelLabel);
    const btnCancelIcon = computed(() => mdiCancel);
    const btnCancelColor = computed(() => props.btnCancelColor);

    const btnConfirmLabel = computed(() => props.delete ? props.btnDeleteLabel : props.btnConfirmLabel);
    const btnConfirmIcon = computed(() => props.delete ? mdiDelete : mdiCheckCircleOutline);
    const btnConfirmColor = computed(() => props.delete ? props.btnDeleteColor : props.btnConfirmColor);

    const emit = defineEmits(['confirmed', 'canceled'])
    const dialog = defineModel('dialog', {type: Boolean, default: false});

    const onConfirmed = () => {
        emit('confirmed');
        (!props.leaveOpen) && (dialog.value = false);
    };

    const onCanceled = () => {
        emit('canceled');
        (!props.leaveOpen) && (dialog.value = false);
    };
</script>
