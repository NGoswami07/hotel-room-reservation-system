<template>
    <div class="hotel-page">
        <div class="page-header">
            <h2>Hotel Reservation System</h2>
        </div>

        <div class="card-box">
            <div class="form-group">
                <label>Number Of Rooms</label>

                <input
                    v-model="roomCount"
                    type="number"
                    min="1"
                    max="5"
                    class="form-control"
                    placeholder="Enter room count"
                />
            </div>

            <div class="button-group">
                <button class="btn btn-success" @click="bookRooms">
                    Book Rooms
                </button>

                <button class="btn btn-warning" @click="randomOccupancy">
                    Random Occupancy
                </button>

                <button class="btn btn-danger" @click="resetAllBookings">
                    Reset All Bookings
                </button>
            </div>

            <div v-if="allocatedRooms.length" class="allocated-box">
                <strong>Allocated Rooms:</strong>
                {{ allocatedRooms.join(", ") }}
            </div>
        </div>

        <div class="hotel-grid">
            <div
                class="floor-row"
                v-for="floor in groupedRooms"
                :key="floor.floor"
            >
                <!-- Lift + Stairs -->

                <div class="lift-section">
                    <div class="lift-box">🛗 Lift</div>

                    <div class="stair-box">🪜 Stair</div>
                </div>

                <!-- Floor Rooms -->

                <div class="floor-content">
                    <div class="floor-title">Floor {{ floor.floor }}</div>

                    <div class="rooms-grid">
                        <div
                            v-for="room in floor.rooms"
                            :key="room.room_number"
                            class="room"
                            :class="{
                                booked: room.is_booked,
                                allocated: allocatedRooms.includes(
                                    room.room_number,
                                ),
                            }"
                        >
                            {{ room.room_number }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            rooms: [],
            roomCount: 1,
            allocatedRooms: [],
        };
    },

    computed: {
        groupedRooms() {
            const grouped = {};

            this.rooms.forEach((room) => {
                if (!grouped[room.floor_number]) {
                    grouped[room.floor_number] = [];
                }

                grouped[room.floor_number].push(room);
            });

            return Object.keys(grouped)
                .sort((a, b) => b - a)
                .map((floor) => ({
                    floor,
                    rooms: grouped[floor],
                }));
        },
    },

    methods: {
        async fetchAllRooms() {
            try {
                const response = await axios.get("/api/all-rooms");

                this.rooms = response?.data?.data;
            } catch (e) {
                alert("Failed to load rooms");
            }
        },

        async bookRooms() {
            try {
                const response = await axios.post("/api/book-rooms", {
                    rooms: this.roomCount,
                });

                this.allocatedRooms = response?.data?.data?.rooms;

                await this.fetchAllRooms();
            } catch (e) {
                // console.log(e);
                alert(
                    e.response?.data?.message || e.message || "Booking failed",
                );
            }
        },

        async randomOccupancy() {
            try {
                await axios.post("/api/random-occupancy");

                this.allocatedRooms = [];

                await this.fetchAllRooms();
            } catch (e) {
                alert("Random occupancy failed");
            }
        },

        async resetAllBookings() {
            try {
                await axios.post("/api/reset-all-bookings");

                this.allocatedRooms = [];

                await this.fetchAllRooms();
            } catch (e) {
                alert("Reset failed");
            }
        },
    },

    mounted() {
        this.fetchAllRooms();
    },
};
</script>

<style scoped>
.hotel-page {
    padding: 20px;
    background: #f4f6f9;
    min-height: 100vh;
}

/* Header */

.page-header {
    margin-bottom: 20px;
}

.page-header h2 {
    font-size: 28px;
    font-weight: 700;
    color: #222;
}

/* Card */

.card-box {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

/* Form */

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}

.form-control {
    width: 250px;
    height: 45px;
    border: 1px solid #ccc;
    border-radius: 6px;
    padding: 10px;
    outline: none;
}

/* Buttons */

.button-group {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.btn {
    border: none;
    padding: 12px 18px;
    border-radius: 6px;
    color: #fff;
    cursor: pointer;
    font-weight: 600;
    transition: 0.3s;
}

.btn:hover {
    opacity: 0.9;
}

.btn-success {
    background: #28a745;
}

.btn-warning {
    background: #f39c12;
}

.btn-danger {
    background: #dc3545;
}

/* Allocated */

.allocated-box {
    margin-top: 20px;
    background: #d4edda;
    color: #155724;
    padding: 12px;
    border-radius: 6px;
}

/* Hotel Grid */

.hotel-grid {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

/* Floor */

.floor-row {
    background: #fff;
    padding: 18px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.floor-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 15px;
    color: #333;
}

/* Rooms */

.rooms-grid {
    display: grid;
    grid-template-columns: repeat(10, 80px);
    gap: 12px;
}

/* Room */

.room {
    width: 80px;
    height: 80px;
    border-radius: 8px;

    display: flex;
    align-items: center;
    justify-content: center;

    font-weight: 700;
    color: white;

    background: #28a745;

    transition: 0.3s;
}

.room:hover {
    transform: scale(1.05);
}

/* Booked */

.room.booked {
    background: #dc3545;
}

/* Allocated */

.room.allocated {
    background: #007bff;
}

@media (max-width: 768px) {
    .rooms-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .room {
        width: 100%;
    }
}
.floor-row {
    display: flex;
    gap: 20px;
    align-items: flex-start;

    background: #fff;
    padding: 18px;
    border-radius: 10px;

    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

/* Left Section */

.lift-section {
    display: flex;
    flex-direction: column;
    gap: 10px;

    min-width: 90px;
}

/* Lift */

.lift-box,
.stair-box {
    height: 60px;

    border-radius: 8px;

    display: flex;
    align-items: center;
    justify-content: center;

    color: white;
    font-weight: 700;

    font-size: 14px;
}

/* Lift */

.lift-box {
    background: #6f42c1;
}

/* Stair */

.stair-box {
    background: #343a40;
}

/* Right Content */

.floor-content {
    flex: 1;
}
</style>
