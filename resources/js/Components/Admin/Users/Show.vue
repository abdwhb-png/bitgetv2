<script setup>
import { ref, watch } from "vue";

// Définir les props avec leur type
const props = defineProps({
    user: {
        type: Object, // On garde le type Object
        required: false, // Permettre que ce soit non requis
        default: null, // Spécifiez une valeur par défaut
    },
    show: {
        type: Boolean,
        default: false,
    },
});

// Définir les événements émis
const emits = defineEmits(["hide"]);

// Utiliser une référence pour suivre la visibilité
const visible = ref(props.show);

const isObject = (value) => {
    return typeof value === "object";
};

// Surveiller le changement de la prop `user`
watch(
    () => props.show,
    (newShow) => {
        // Vérifier si `user` n'est pas null et contient des clés
        visible.value = newShow;
    },
    { immediate: true } // Appliquer immédiatement au montage
);
</script>

<template>
    <div>
        <Dialog
            @hide="emits('hide')"
            v-model:visible="visible"
            modal
            :dismissable-mask="true"
            :style="{ width: '50rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <!-- En-tête du dialogue -->
            <template #header>
                <h3 class="text-lg font-medium text-sky-900 dark:text-sky-700">
                    Show
                    <span class="underline text-sky-600 dark:text-sky-500">
                        {{ user?.full_name || "N/A" }}
                    </span>
                    information.
                </h3>
            </template>

            <!-- Contenu principal -->
            <div>
                <Accordion value="0">
                    <AccordionPanel
                        v-for="(key, index) in Object.keys(user).sort()"
                        :key="index"
                        :value="index"
                    >
                        <AccordionHeader v-if="user[key] !== null">
                            <span>{{ key }}</span>
                        </AccordionHeader>

                        <AccordionContent v-if="user[key] !== null">
                            <pre v-if="!isObject(user[key])">{{
                                user[key]
                            }}</pre>
                            <ul v-else class="list-group">
                                <li
                                    v-for="(item, index) in user[key]"
                                    :key="index"
                                    class="list-group-item"
                                >
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">
                                            {{ index + ":" }}
                                        </span>
                                        <span class="ml-2">
                                            {{ item }}
                                        </span>
                                    </div>
                                </li>
                            </ul>
                        </AccordionContent>
                    </AccordionPanel>
                </Accordion>
            </div>
        </Dialog>
    </div>
</template>
