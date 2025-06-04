    <template>
        <div>
            <h3>Edit Person</h3>

            <!-- Step 1: Input index -->
            <input type="number" v-model.number="editIndex" placeholder="Enter index to edit" @input="loadPerson" />

            <!-- Step 2: Feedback -->
            <div class="feedback">
                <em v-if="!validIndex">Enter an index to load person data</em>
                <em v-else>Editing: {{ person.name }} (Index {{ editIndex }})</em>
            </div>

            <!-- Step 3: Form -->
            <div v-if="validIndex" class="edit-form-section">
                <input v-model="person.name" placeholder="Name" type="text" />
                <input v-model.number="person.yob" placeholder="Year Born (YYYY)" type="number" />
                <input v-model.number="person.weight" placeholder="Weight (kg)" type="number" />
                <input v-model.number="person.height" placeholder="Height (cm)" type="number" />
                <input v-model="person.photoUrl" placeholder="Image URL" type="text" />

                <div v-if="errorMessage" class="error">{{ errorMessage }}</div>

                <button @click="updatePerson">Update</button>
            </div>
        </div>
    </template>

    <script>
    export default {
        name: "EditPerson",
        props: ["personList"],
        data() {
            return {
                editIndex: null,
                person: {},
                validIndex: false,
                errorMessage: "",
            };
        },
        methods: {
            loadPerson() {
                const idx = this.editIndex;

                if (
                    idx === null ||
                    idx === '' ||
                    isNaN(idx) ||
                    idx < 0 ||
                    idx >= this.personList.length
                ) {
                    this.person = {};
                    this.validIndex = false;
                    this.errorMessage = "";
                    return;
                }

                // Copy full person object including id
                this.person = { ...this.personList[idx] };
                this.validIndex = true;
                this.errorMessage = "";
            },

            validate() {
                const year = new Date().getFullYear();
                if (!this.person.name || !this.person.name.trim())
                    return "Name cannot be empty.";
                if (
                    !this.person.yob ||
                    this.person.yob < 1900 ||
                    this.person.yob > year
                )
                    return "Year Born must be between 1900 and current year.";
                if (!this.person.weight || this.person.weight <= 0)
                    return "Weight must be positive.";
                if (!this.person.height || this.person.height <= 0)
                    return "Height must be positive.";
                return "";
            },
            async updatePerson() {
                this.errorMessage = this.validate();
                if (this.errorMessage) return;

                const heightM = this.person.height / 100;
                this.person.bmi = (this.person.weight / (heightM * heightM)).toFixed(2);
                this.person.age = new Date().getFullYear() - this.person.yob;

                try {
                    const response = await fetch(
                        `http://localhost:8085/person/${this.person.id}`,
                        {
                            method: "PUT",
                            headers: {
                                "Content-Type": "application/json",
                            },
                            body: JSON.stringify(this.person),
                        }
                    );

                    if (!response.ok) {
                        throw new Error(`Failed to update person: ${response.statusText}`);
                    }

                    // Optionally, get the updated person from the response
                    // const updatedPerson = await response.json();

                    // Notify parent with id and updated person data
                    this.$emit("update-person", this.person.id, this.person);

                    alert("Updated successfully.");
                    this.validIndex = false;
                    this.editIndex = null;
                    this.person = {};
                } catch (error) {
                    this.errorMessage = error.message;
                }
            }

        },
    };
    </script>