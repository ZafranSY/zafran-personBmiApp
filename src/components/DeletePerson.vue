<template>
    <div>
        <h3>Delete Person by Index</h3>
        <input type="number" id="deleteIndex" v-model.number="deleteIndex" placeholder="Enter index to delete" />
        <div id="deleteFeedback" class="feedback">
            <em v-if="!isValidIndex"> Enter a valid index to delete</em>
            <em v-else> Ready to delete: {{ personList[deleteIndex].name }}</em>
            <button v-if="isValidIndex" @click="deletePerson">Delete Person</button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        personList: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            deleteIndex: null,
        };
    },
    computed: {
        isValidIndex() {
            return (
                this.deleteIndex !== null &&
                this.deleteIndex >= 0 &&
                this.deleteIndex < this.personList.length
            );
        },
    },
    methods: {
        async deletePerson() {
            if (this.isValidIndex) {
                const person = this.personList[this.deleteIndex];
                if (
                    confirm(`Are you sure you want to delete ${person.name}?`)
                ) {
                    try {
                        const response = await fetch(
                            `http://localhost:8085/person/${person.id}`,
                            {
                                method: 'DELETE',
                            }
                        );
                        if (!response.ok) {
                            throw new Error('Failed to delete person.');
                        }

                        // Notify parent to update list
                        this.$emit('delete-person', this.deleteIndex);
                        this.deleteIndex = null;
                    } catch (error) {
                        alert('Error deleting person: ' + error.message);
                    }
                }
            }
        },
    },
};
</script>