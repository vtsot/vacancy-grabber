<template>
    <div
        v-html="nl2br(html)"
        :style="style"
    />
</template>

<script setup lang="ts">
    import {nl2br} from "../../utils/HtmlUtils";
    import {computed} from "vue";

    const props = defineProps({
        html: {type: [String, null], required: false, default: null},
        maxWidth: {type: [String, Number, null], default: '600px'},
        maxHeight: {type: [String, Number, null], default: '300px'},
    });

    const style = computed(() => {
        return {
            'max-width': props.maxWidth,
            'max-height': props.maxHeight,
            'overflow-y': 'auto',
            'overflow-wrap': 'break-word',
            'white-space': 'normal',
        };
    });
</script>
