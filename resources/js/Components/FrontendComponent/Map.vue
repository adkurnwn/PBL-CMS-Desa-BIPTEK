<script setup>
import { ref, onMounted, watch, computed } from "vue";
import "leaflet/dist/leaflet.css";
import { LMap, LTileLayer, LMarker, LPolygon } from "@vue-leaflet/vue-leaflet";
import axios from "axios";

// Define props
const props = defineProps({
    locationName: {
        type: String,
        default: "INDONESIA",
    },
    initialZoom: {
        type: Number,
        default: 13, // Higher default zoom level
    },
});

// Process location name to improve search results
const processedLocationName = computed(() => {
    if (!props.locationName) return "INDONESIA";

    // 1. Remove "Desa" prefix (case insensitive)
    // 2. Ensure spaces are properly encoded and handled
    // 3. Add "Indonesia" suffix to improve geolocation context
    let processed = props.locationName.replace(/desa\s*/i, "").trim();

    // Add Indonesia as context to improve search results
    if (!processed.toLowerCase().includes("indonesia")) {
        processed += ", Indonesia";
    }

    return processed;
});

// Fix Leaflet icon issue
import L from "leaflet";
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: new URL(
        "leaflet/dist/images/marker-icon-2x.png",
        import.meta.url
    ).href,
    iconUrl: new URL("leaflet/dist/images/marker-icon.png", import.meta.url)
        .href,
    shadowUrl: new URL("leaflet/dist/images/marker-shadow.png", import.meta.url)
        .href,
});

const mapCenter = ref(null);
const regionPolygon = ref([]);
const map = ref(null);
const zoom = ref(5); // Default zoom (will be changed when location is found)
const center = ref([-2.5489, 118.0149]); // Default center of Indonesia

// Add satellite layer options
const satelliteLayerUrl =
    "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}";
const satelliteLayerAttribution =
    "Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community";

const searchLocation = async () => {
    try {
        const searchQuery = processedLocationName.value;
        console.log("Searching for location:", searchQuery);

        const response = await axios.get(
            `https://nominatim.openstreetmap.org/search`,
            {
                params: {
                    q: searchQuery,
                    format: "json",
                    polygon_geojson: 1,
                    countrycodes: "id",
                    limit: 1, // Limit to one result for better accuracy
                },
            }
        );

        if (response.data.length > 0) {
            const place = response.data[0];
            mapCenter.value = [parseFloat(place.lat), parseFloat(place.lon)];

            // Update center to the found location
            center.value = mapCenter.value;

            // Set zoom level for better view of the area
            zoom.value = props.initialZoom;

            if (place.geojson) {
                if (place.geojson.type === "Polygon") {
                    regionPolygon.value = place.geojson.coordinates[0].map(
                        (coord) => [coord[1], coord[0]]
                    );
                } else if (place.geojson.type === "MultiPolygon") {
                    regionPolygon.value = place.geojson.coordinates[0][0].map(
                        (coord) => [coord[1], coord[0]]
                    );
                }

                // Ensure map is available before attempting to fit bounds
                if (map.value && regionPolygon.value.length > 0) {
                    // Use setTimeout to ensure map is fully initialized
                    setTimeout(() => {
                        // Fit bounds with padding
                        map.value.fitBounds(regionPolygon.value, {
                            padding: [50, 50],
                            maxZoom: 15,
                        });
                    }, 100);
                }
            } else {
                // If no polygon data, at least center and zoom to the marker
                if (map.value) {
                    setTimeout(() => {
                        map.value.setView(mapCenter.value, props.initialZoom);
                    }, 100);
                }
            }
        } else {
            console.warn(`No location found for: ${searchQuery}`);

            // Try a more generalized search by removing spaces
            const fallbackQuery = searchQuery.replace(/\s+/g, "");
            console.log("Trying fallback search:", fallbackQuery);

            const fallbackResponse = await axios.get(
                `https://nominatim.openstreetmap.org/search`,
                {
                    params: {
                        q: fallbackQuery,
                        format: "json",
                        polygon_geojson: 1,
                        countrycodes: "id",
                        limit: 1,
                    },
                }
            );

            if (fallbackResponse.data.length > 0) {
                const place = fallbackResponse.data[0];
                mapCenter.value = [
                    parseFloat(place.lat),
                    parseFloat(place.lon),
                ];
                center.value = mapCenter.value;
                zoom.value = props.initialZoom;

                // Process polygon or point data similar to above
                if (place.geojson) {
                    // Same polygon processing as above
                    if (place.geojson.type === "Polygon") {
                        regionPolygon.value = place.geojson.coordinates[0].map(
                            (coord) => [coord[1], coord[0]]
                        );
                    } else if (place.geojson.type === "MultiPolygon") {
                        regionPolygon.value =
                            place.geojson.coordinates[0][0].map((coord) => [
                                coord[1],
                                coord[0],
                            ]);
                    }

                    if (map.value && regionPolygon.value.length > 0) {
                        setTimeout(() => {
                            map.value.fitBounds(regionPolygon.value, {
                                padding: [50, 50],
                                maxZoom: 15,
                            });
                        }, 100);
                    }
                } else if (map.value) {
                    setTimeout(() => {
                        map.value.setView(mapCenter.value, props.initialZoom);
                    }, 100);
                }
            } else {
                console.error(
                    "Both search queries failed for:",
                    props.locationName
                );
            }
        }
    } catch (error) {
        console.error("Error fetching location:", error);
    }
};

// Watch for changes in locationName
watch(
    processedLocationName,
    (newValue) => {
        if (newValue && map.value && newValue !== "INDONESIA") {
            searchLocation();
        }
    },
    { immediate: false }
);

const onMapReady = (mapObject) => {
    map.value = mapObject;

    // Only search if we have a location name
    if (
        processedLocationName.value &&
        processedLocationName.value !== "INDONESIA"
    ) {
        searchLocation();
    }
};
</script>

<template>
    <div>
        <l-map
            v-model:zoom="zoom"
            v-model:center="center"
            style="height: 500px; width: 100%"
            @ready="onMapReady"
        >
            <l-tile-layer
                :url="satelliteLayerUrl"
                :attribution="satelliteLayerAttribution"
            />
            <l-marker v-if="mapCenter" :lat-lng="mapCenter" />
            <l-polygon
                v-if="regionPolygon.length"
                :lat-lngs="regionPolygon"
                :color="'#ff4500'"
                :fill="true"
                :fill-opacity="0.3"
                :weight="2"
            />
        </l-map>
    </div>
</template>

<style scoped>
.input-box {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    font-size: 16px;
}
</style>
