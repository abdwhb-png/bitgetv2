<script setup>
import { ref } from "vue";
import PMethodForm from "./PMethodForm.vue";

const showAdd = ref(false);

const props = defineProps({
    datas: Object,
});
</script>

<template>
    <div class="flex justify-end">
        <Button
            label="Add Payment Method"
            icon="pi pi-plus"
            size="small"
            severity="info"
            @click="showAdd = true"
        />
        <Dialog
            v-model:visible="showAdd"
            modal
            header="Create Payment Method"
            :style="{ width: '50rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <PMethodForm @created="showAdd = false" />
        </Dialog>
    </div>
    <Accordion value="">
        <AccordionPanel
            v-for="(item, index) in datas"
            :key="index"
            :value="index"
        >
            <AccordionHeader>
                {{ item.name }}
            </AccordionHeader>
            <AccordionContent>
                <PMethodForm :item="item" />
            </AccordionContent>
        </AccordionPanel>
    </Accordion>
</template>
