class GoogleMap {
    route = {
        name: null,
        wayPoints: [],
        objects : [],
        requests : []
    };
    constructor(options) {
        const {
            mapElement, markersElement
        } = options;
        this.markerDirections = [];
        this.map = new google.maps.Map(mapElement, {
            zoom: 8,
            // Center the map on spain
            center: { lat: 40.416775, lng: -3.703339 },
        });
        this.routeManager = new google.maps.DirectionsService();
        if (markersElement) {
            this.markersElement = markersElement;
        }
        this.initMarkerManager();
    }
    getData() {
        return {
            directions : this.getRouteDirections(),
            sourceRequests : this.route.requests
        };
    }
    /**
     * Function to initialize click events on map
     * Mainly related with the creation of markers
     */
    initMarkerManager() {
        google.maps.event.addListener(this.map, "click", async (event) => {
            const marker = this.renderMapMarker(event.latLng, this.route.wayPoints.length);
            // We keep the markers in memory to remove them when is needed
            this.route.objects.push(marker);
            // Set waypoints into our object
            // This waypoints are going to be used to render directions
            this.route.wayPoints.push(event.latLng);
        });
    }
    renderMapMarker(location, markerIndex, markerOptions = {}){
        return new google.maps.Marker({
            ...markerOptions,
            position: location,
            map: this.map,
            label : {
                text : `${markerIndex + 1}`,
                color : 'white'
            }
        });
    }
    /**
     * Function to clean the map
     */
    emptyMap(){
        this.routeDrawer.setDirections({});
    }
    chunkWayPoints(sourceWayPoints){
        let wayPoints = [];
        let index = -1;
        let counter = 0;
        for (const key in sourceWayPoints) {
            if (counter % 26 == 0) {
                index++;
                wayPoints[index] = [];
            };
            const element = sourceWayPoints[key];
            wayPoints[index].push(element);
            counter++;
        }
        return wayPoints;
    }
    /**
     * Function to render the waypoints as a directions
     */
    generateRoute(existingDirections = []){
        this.route.objects.forEach((object) => {
            object.setMap(null);
        });
        const wayPoints = Object.assign([], this.route.wayPoints);
        this.route.wayPoints = [];
        this.markerDirections = [];
        this.route.requests = existingDirections;
        this.chunkWayPoints(wayPoints).reduce(async (previous, chunkWayPoints, index) => {
            const previousRequest = await previous;
            const startPoint = previousRequest.request.destination !== undefined ? previousRequest.request.destination.location : chunkWayPoints[0];
            const endPoint = chunkWayPoints[chunkWayPoints.length - 1];
            const wayPoints = chunkWayPoints
                .filter((marker) => marker !== startPoint && marker !== endPoint)
                .map(marker => { return { location : marker } });
            let directions = null;
            const renderer = new google.maps.DirectionsRenderer({
                draggable: true,
                map : this.map,
                suppressMarkers: true
            });
            this.route.objects.push(renderer);
            if (this.route.requests[index] !== undefined) {
                directions = this.route.requests[index];
            } else {
                directions = await this.routeManager.route({
                    origin : startPoint,
                    destination : endPoint,
                    travelMode: google.maps.TravelMode.DRIVING,
                    provideRouteAlternatives: true,
                    waypoints : wayPoints
                });
                this.route.requests.push(directions);
            }
            renderer.setDirections(directions);
            this.markerDirections = this.markerDirections.concat(directions.routes[0].legs);
            this.route.wayPoints.push(this.toJSON(directions.request.origin.location));
            this.route.wayPoints = this.route.wayPoints.concat(directions.request.waypoints.map((waypoint) => this.toJSON(waypoint.location.location)));
            this.route.wayPoints.push(this.toJSON(directions.request.destination.location));
            return directions;
        }, Promise.resolve({
            request : {}
        })).then(() => {
            const renderedPoints = [];
            this.route.wayPoints.forEach((wayPoint, index) => {
                // Hide markers on the same spot, this could happens for routes with more than 26 waypoints
                const locationIndex = `${wayPoint.lat}-${wayPoint.lng}`;
                if (renderedPoints.includes(locationIndex)) {
                    return;
                }
                this.route.objects.push(this.renderMapMarker(wayPoint, index));
                renderedPoints.push(locationIndex);
            });
            this.renderMarkers();
        }).catch(() => {
            console.log(arguments);
            alert("Ruta invalida intente nuevamente!");
        });
    }
    toJSON(element){
        if (element.constructor.name !== 'Object') return element.toJSON();
        return element;
    }
   /**
    * We handle directions and transform it into a valid waypoints
    */
    getRouteDirections(){
        const directions = this.markerDirections;
        const directionsNb = directions.length - 1;
        const markers = [];
        for (const key in directions) {
            const element = directions[key];
            const targets = key == directionsNb ? ['start', 'end'] :  ['start'];
            targets.forEach((target) => {
                markers.push({
                    address : element[`${target}_address`],
                    distanceToNextPoint : key == directionsNb && target == 'end' ? null : element.distance,
                    location : this.toJSON(element[`${target}_location`])
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
        const renderedMarkers = [];
        directions.forEach((route, index) => {
            const locationIndex = `${route.location.lat}-${route.location.lng}`;
            // Ignore markers on the same spot, this could happens for routes with more than 26 waypoints
            if (renderedMarkers.includes(locationIndex)) {
                return;
            }
            const placeText = $(`<span>${route.address}</span>`)
                .addClass("text-truncate ps-2 d-inline-block")
                .css({maxWidth : 'calc(100% - 30px)'})
                .attr("title", route.address);
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
            renderedMarkers.push(locationIndex);
        });
    }
}