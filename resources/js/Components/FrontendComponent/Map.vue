<script setup>
import { ref, onMounted } from "vue";
import "leaflet/dist/leaflet.css";
import { LMap, LTileLayer, LMarker, LPolygon } from "@vue-leaflet/vue-leaflet";
import axios from "axios";

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

const searchLocation = async () => {
    try {
        const response = await axios.get(
            `https://nominatim.openstreetmap.org/search`,
            {
                params: {
                    q: "DUKUH PAKIS",
                    format: "json",
                    polygon_geojson: 1,
                    countrycodes: "id",
                },
            }
        );

        if (response.data.length > 0) {
            const place = response.data[0];
            mapCenter.value = [parseFloat(place.lat), parseFloat(place.lon)];

            // Set zoom level for better view of the area
            zoom.value = 13;
            // Update center to the found location
            center.value = mapCenter.value;

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
                if (map.value && regionPolygon.value.length > 0) {
                    // Fit bounds with padding
                    map.value.fitBounds(regionPolygon.value, {
                        padding: [50, 50],
                        maxZoom: 15,
                    });
                }
            } else {
                // If no polygon data, at least center and zoom to the marker
                if (map.value) {
                    map.value.setView(mapCenter.value, 13);
                }
            }
        }
    } catch (error) {
        console.error("Error fetching location:", error);
    }
};

const getRegionBoundary = async (osm_id, osm_type) => {
    try {
        const type =
            osm_type === "R" ? "relation" : osm_type === "W" ? "way" : "node";
        const query = `
      [out:json][timeout:25];
      area[name="Indonesia"]->.indonesia;
      ${type}(${osm_id})(area.indonesia);
      out geom;
    `;

        const response = await axios.get(
            `https://overpass-api.de/api/interpreter?data=${encodeURIComponent(
                query
            )}`
        );

        if (response.data.elements && response.data.elements.length > 0) {
            const element = response.data.elements[0];
            if (element.geometry) {
                regionPolygon.value = element.geometry.map((coord) => [
                    coord.lat,
                    coord.lon,
                ]);
                if (map.value && regionPolygon.value.length > 0) {
                    // Fit bounds with padding
                    map.value.fitBounds(regionPolygon.value, {
                        padding: [50, 50],
                        maxZoom: 15,
                    });
                }
            }
        }
    } catch (error) {
        console.error("Error fetching region boundary:", error);
    }
};

const onMapReady = (mapObject) => {
    map.value = mapObject;
    searchLocation();
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
                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            />
            <l-marker v-if="mapCenter" :lat-lng="mapCenter" />
            <l-polygon
                v-if="regionPolygon.length"
                :lat-lngs="regionPolygon"
                :color="'red'"
                :fill="true"
                :fill-opacity="0.4"
            />
        </l-map>
    </div>
</template>

<style>
.input-box {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    font-size: 16px;
}
</style>
