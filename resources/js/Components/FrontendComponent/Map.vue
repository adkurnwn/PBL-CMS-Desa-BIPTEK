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
        default: 15,
    },
    locationData: Object,
    kelurahan: String,
    kodePos: String,
});

const locationName = computed(() => {
    if (!props.locationName || props.locationName === "INDONESIA") {
        return null;
    }

    // Clean up the location name string
    return props.locationName
        .replace(/Desa\s+/gi, "")
        .replace(/Kabupaten\s+/gi, "")
        .replace(/Kecamatan\s+/gi, "")
        .replace(/Kota\s+/gi, "")
        .trim();
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
const zoom = ref(5);
const center = ref([-2.5489, 118.0149]);

const satelliteLayerUrl =
    "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}";
const satelliteLayerAttribution =
    "Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community";

const searchWithQuery = async (query, includePolygon = true) => {
    try {
        const params = {
            q: query,
            format: "json",
            countrycodes: "id",
            limit: 5, // Get more results to choose from
        };

        if (includePolygon) {
            params.polygon_geojson = 1;
        }

        const response = await axios.get(
            `https://nominatim.openstreetmap.org/search`,
            { params }
        );

        if (response.data.length > 0) {
            // Sort results by importance/class to get the best match
            const sortedResults = response.data.sort((a, b) => {
                const aImportance = parseFloat(a.importance || 0);
                const bImportance = parseFloat(b.importance || 0);
                return bImportance - aImportance;
            });

            return sortedResults[0];
        }

        return null;
    } catch (error) {
        console.error("Search error for query:", query, error);
        return null;
    }
};

const processLocationResult = (place) => {
    if (!place) return false;

    mapCenter.value = [parseFloat(place.lat), parseFloat(place.lon)];
    center.value = mapCenter.value;
    zoom.value = props.initialZoom;

    if (place.geojson) {
        try {
            if (place.geojson.type === "Polygon") {
                regionPolygon.value = place.geojson.coordinates[0].map(
                    (coord) => [coord[1], coord[0]]
                );
            } else if (place.geojson.type === "MultiPolygon") {
                regionPolygon.value = place.geojson.coordinates[0][0].map(
                    (coord) => [coord[1], coord[0]]
                );
            }

            if (map.value && regionPolygon.value.length > 0) {
                setTimeout(() => {
                    map.value.fitBounds(regionPolygon.value, {
                        padding: [50, 50],
                        maxZoom: 15,
                    });
                }, 100);
            }
        } catch (error) {
            console.error("Error processing polygon data:", error);
            // Fall back to just showing the marker
            if (map.value) {
                setTimeout(() => {
                    map.value.setView(mapCenter.value, props.initialZoom);
                }, 100);
            }
        }
    } else {
        // No polygon data, just center on the marker
        if (map.value) {
            setTimeout(() => {
                map.value.setView(mapCenter.value, props.initialZoom);
            }, 100);
        }
    }

    return true;
};

const searchLocation = async () => {
    try {
        const searchQueries = createSearchQueries();

        // Try each query in order of specificity
        for (const query of searchQueries) {
            // First try with polygon data
            let result = await searchWithQuery(query, true);

            if (result) {
                if (processLocationResult(result)) {
                    return;
                }
            }

            // If no polygon result, try without polygon for faster response
            result = await searchWithQuery(query, false);

            if (result) {
                if (processLocationResult(result)) {
                    return;
                }
            }

            // Add a small delay between requests to be respectful to the API
            await new Promise((resolve) => setTimeout(resolve, 100));
        }

        console.warn(
            "No location found for any search queries:",
            searchQueries
        );

        // Final fallback - try a very general search
        const fallbackQuery = "Indonesia";
        const fallbackResult = await searchWithQuery(fallbackQuery, false);
        if (fallbackResult) {
            processLocationResult(fallbackResult);
        }
    } catch (error) {
        console.error("Error in location search:", error);
    }
};

// Create search queries for better location finding
const createSearchQueries = () => {
    const queries = [];

    // First priority: Most specific location using all available props
    if (locationName.value) {
        queries.push(locationName.value);
    }

    // Second priority: Fallback to kelurahan + kodePos if available
    if (props.kelurahan && props.kodePos) {
        const cleanedKelurahan = props.kelurahan
            .replace(/^Desa\s+/i, "")
            .trim();
        queries.push(`${cleanedKelurahan}, ${props.kodePos}`);
    }

    // Remove duplicates and empty queries
    return [...new Set(queries.filter((q) => q && q.trim() !== ""))];
};

// Watch for changes in location props
watch(
    [() => props.locationName, () => props.kelurahan],
    () => {
        if (map.value) {
            searchLocation();
        }
    },
    { immediate: false }
);

const onMapReady = (mapObject) => {
    map.value = mapObject;

    // Only search if we have location data
    if (props.locationName && props.locationName !== "INDONESIA") {
        searchLocation();
    }
};
</script>

<template>
    <div class="w-full h-[250px] sm:h-[300px] md:h-[400px] xl:h-[450px]">
        <l-map
            v-model:zoom="zoom"
            v-model:center="center"
            style="width: 100%; height: 100%"
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
