<template>
    <div>
        <h3>Add Person</h3>
        <form @submit.prevent="handleSubmit" id="bmiForm">
            Fullname :
            <input type="text" v-model="form.name" placeholder="Name" required />

            Year Born :
            <input type="text" v-model="form.yob" placeholder="yyyy" required />

            Weight :
            <input type="number" v-model.number="form.weight" placeholder="Weight (kg)" required />

            Height :
            <input type="number" v-model.number="form.height" placeholder="Height (cm)" required />

            Photo URL :
            <input type="text" v-model.trim="form.photoUrl" placeholder="Image URL" />

            <button type="submit">Add Person</button>
        </form>
    </div>
</template>
<style>
h3 {
    font-size: 20px;
}

</style>
<script>
export default {
    emits: ['person-added'],
    data() {
        return {
            form: {
                name: '',
                yob: '',
                weight: null,
                height: null,
                photoUrl: ''
            }
        };
    },
    methods: {
        async handleSubmit() {
            const yob = parseInt(this.form.yob);
            const age = new Date().getFullYear() - yob;
            const bmi = (
                this.form.weight / ((this.form.height / 100) ** 2)
            ).toFixed(2);

            const person = {
                name: this.form.name,
                yob: yob,
                age: age,
                weight: this.form.weight,
                height: this.form.height,
                bmi: bmi,
                category: 't.b.c',
                photoUrl: this.form.photoUrl
            };
            console.log(JSON.stringify(person))
            try {
                const response = await fetch('http://localhost:8085/person', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(person)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                console.log('Person added successfully!');
                // Optionally emit after success
                this.$emit('person-added', person);
            } catch (error) {
                console.error('Failed to add person:', error);
            }

            // Emit to parent
            this.$emit('person-added', person);

            // Reset form
            this.form = {
                name: '',
                yob: '',
                weight: null,
                height: null,
                photoUrl: ''
            };
        }
    }
};
</script>