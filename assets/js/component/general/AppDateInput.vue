<template>
    <v-text-field
        v-model="model"
        type="date"
        :label="props.label as string"
        :loading="loading"
        :clearable="props.clearable"
    />
</template>


<script setup lang="ts">
    import {watch} from 'vue'

    const props = defineProps({
        loading: {type: Boolean, default: false},
        clearable: {type: Boolean, default: true},

        maxWidth: {type: [String, Number], default: 200},
        minWidth: {type: [String, Number], default: 200},

        label: {type: [String, null], default: 'Date'},
    });

    const emit = defineEmits(['update:modelValue']);
    const model = defineModel('model', {type: String, default: null});
    watch(
        () => model.value,
        (value) => emit('update:modelValue', value),
    );
</script>
