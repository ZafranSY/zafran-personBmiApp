<template>
    <div>
        <h3>View One Person</h3>
        <input type="number" id="indexInput" v-model.number="indexInput" placeholder="Enter person ID" />
        <div id="onePersonView" class="detail-box">
            <template v-if="selectedPerson">
                <p>Name: {{ selectedPerson.name }}</p>
                <p>Year of Birth: {{ selectedPerson.yob }}</p>
                <p>Age: {{ selectedPerson.age }}</p>
                <p>Weight: {{ selectedPerson.weight }} kg</p>
                <p>Height: {{ selectedPerson.height }} cm</p>
                <p>BMI: {{ selectedPerson.bmi }}</p>
                <img v-if="selectedPerson.photoUrl" :src="selectedPerson.photoUrl" alt="Person photo"
                    class="person-photo" />
            </template>
            <em v-else>No person found for that ID</em>
        </div>
    </div>
</template>

<script>
export default {
    name: "viewPerson",
    data() {
        return {
            indexInput: null,
            selectedPerson: null,
            error: null,
        };
    },
    watch: {
        indexInput: {
            immediate: true,
            handler(newVal) {
                if (newVal !== null && newVal !== "") {
                    this.fetchPersonById(newVal);
                } else {
                    this.selectedPerson = null;
                }
            },
        },
    },
    methods: {
        async fetchPersonById(id) {
            try {
                this.error = null;
                this.selectedPerson = null;

                const response = await fetch(`http://localhost:8085/person/${id}`, {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                    },
                });

                if (!response.ok) {
                    // Person not found or server error
                    this.selectedPerson = null;
                    this.error = `Error fetching person (status: ${response.status})`;
                    return;
                }

                const data = await response.json();
                this.selectedPerson = data;
            } catch (err) {
                this.error = "Failed to fetch person data.";
                console.error(err);
            }
        },
    },
};
</script>   