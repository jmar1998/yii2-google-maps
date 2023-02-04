class GoogleMap {
    route = {
        name: null,
        wayPoints: [],
        markers : [],
        realWaypoints : []
    };
    constructor(options) {
        const {
            mapElement, markersElement
        } = options;
        this.map = new google.maps.Map(mapElement, {
            zoom: 8,
            // Center the map on spain
            center: { lat: 40.416775, lng: -3.703339 },
        });
        this.routeManager = new google.maps.DirectionsService();
        this.routeDrawer = new google.maps.DirectionsRenderer({
            draggable: true,
        });
        if (markersElement) {
            this.markersElement = markersElement;
            this.routeDrawer.addListener("directions_changed", () => {
                this.renderMarkers();
            });
        }
        this.routeDrawer.setMap(this.map);
        this.initMarkerManager();
    }
    getData() {
        return this.getRouteDirections();
    }
    /**
     * Function to initialize click events on map
     * Mainly related with the creation of markers
     */
    initMarkerManager() {
        google.maps.event.addListener(this.map, "click", async (event) => {
            const labels = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            const marker = new google.maps.Marker({
                position: event.latLng,
                map: this.map,
                label : {
                    text : labels[this.route.wayPoints.length],
                    color : 'white'
                }
            });
            // We keep the markers in memory to remove them when is needed
            this.route.markers.push(marker);
            // Set waypoints into our object
            // This waypoints are going to be used to render directions
            this.route.wayPoints.push(event.latLng);
        });
    }
    /**
     * Function to clean the map
     */
    emptyMap(){
        this.routeDrawer.setDirections({});
    }
    /**
     * Function to render the waypoints as a directions
     */
    generateRoute(){
        this.route.markers.forEach((marker) => {
            marker.setMap(null);
        });
        // We gather the first and last waypoint to use them as a origin
        const startPoint = this.route.wayPoints[0];
        const endPoint = this.route.wayPoints[this.route.wayPoints.length - 1];
        const wayPoints = this.route.wayPoints
            .filter((marker) => marker !== startPoint && marker !== endPoint)
            .map(marker => {
                return {
                    location : marker
                };
            });
        this.routeManager.route({
            origin : startPoint,
            destination : endPoint,
            travelMode: google.maps.TravelMode.DRIVING,
            provideRouteAlternatives: true,
            waypoints : wayPoints
        }, (directions, status) => {
            if (status == 'OK') {
                // When there is a match directions we render it into the map and set the "REAL" waypoints from the directions API
                // Is very possible that google api needs to move the point to be a valid point
                this.routeDrawer.setDirections(directions);
                const realWaypoints = this.getRouteDirections().map((direction) => direction.location);
                // We keep the realWaypoints in memory to allow rollback
                this.route.realWaypoints = realWaypoints;
                this.route.wayPoints = Object.assign([], this.route.realWaypoints);
                // Render markers from left panel
                this.renderMarkers();
            } else {
                // In the case of invalid route, we rollback the waypoints
                this.route.wayPoints = this.route.realWaypoints;
                alert("Ruta invalida intente nuevamente!");
            }
        });
    }
   /**
    * We handle directions and transform it into a valid waypoints
    */
    getRouteDirections(){
        const directions = this.routeDrawer.directions.routes[this.routeDrawer.routeIndex].legs;
        const directionsNb = directions.length - 1;
        const markers = [];
        for (const key in directions) {
            const element = directions[key];
            const targets = key == directionsNb ? ['start', 'end'] :  ['start'];
            targets.forEach((target) => {
                markers.push({
                    address : element[`${target}_address`],
                    distanceToNextPoint : key == directionsNb && target == 'end' ? null : element.distance,
                    location : element[`${target}_location`].toJSON()
                })
            });
        };
        return markers;
    }
    /**
     * Function to render markers from left panel
     */
    renderMarkers(){
        $(this.markersElement).empty();
        const directions = this.getRouteDirections();
        directions.forEach((route, index) => {
            const placeText = $(`<span>${route.address}</span>`)
                .addClass("text-truncate ps-2 d-inline-block")
                .css({maxWidth : 'calc(100% - 30px)'});
            const placeItem = $(`<li></li>`);
            const deleteButton = $("<button>X</button>");
            deleteButton
                .addClass("btn btn-danger btn-sm remove-marker")
                .on("click", () => {
                    this.route.wayPoints = this.route.wayPoints.filter((wayPoint) => {
                        return wayPoint.lat != route.location.lat || wayPoint.lng != route.location.lng;
                    });
                    this.generateRoute();
                });
            placeItem
                .addClass("position-relative border rounded mb-1 pt-0 pb-0")
                .append(placeText)
                .append(deleteButton);
            $(this.markersElement).append(placeItem);
            if (index < (directions.length - 1)) {
                $(this.markersElement).append(`
                    <div class="position-relative text-center">
                        <span class="distance-bar"></span>
                        <span class="distance-text badge bg-secondary">${route.distanceToNextPoint.text}</span>
                    </div>
                `);
            }
        });
    }
}