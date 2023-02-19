class DistanceOverlay extends google.maps.OverlayView {
    constructor(params) {
        super();
        this.setMap(params.map);
    }
    setDistance(distance, coordinates){
        this.text = distance;
        this.bounds = coordinates;
        this.draw();
    }
    onAdd() {
        this.span = document.createElement("span");
        this.span.style.borderStyle = "none";
        this.span.style.borderWidth = "0px";
        this.span.style.position = "absolute";
        this.span.textContent = this.text;
        // Add the element to the "overlayLayer" pane.
        const panes = this.getPanes();
        panes.floatPane.appendChild(this.span);
    }
    draw() {
        const overlayProjection = this.getProjection();
        if (!this.bounds || !this.text || !overlayProjection) {
            return;
        }
        const point1 = overlayProjection.fromLatLngToDivPixel(this.bounds.getSouthWest());
        const point2 = overlayProjection.fromLatLngToDivPixel(this.bounds.getNorthEast());
        this.span.textContent = this.text;
        // Get middle point coordinates
        const middlePointX = ((point1.x + point2.x) / 2);
        const middlePointY = ((point1.y + point2.y) / 2);
        // Calculate the angle to fix the text rotation
        const angle = Math.atan((point2.y - point1.y)/ (point2.x - point1.x)) * (180 / Math.PI)
        $(this.span).css({
            left : (middlePointX) + "px",
            fontSize : '14px',
            top : middlePointY + "px",
            fontWeight : 'bold',
            backgroundColor: "rgba(255, 255, 255, 0.5)",
            transform : `rotate(${angle}deg)`
        });
    }
    onRemove() {
        this.span.parentNode.removeChild(this.span);
        delete this.span;
    }
    hide() {
        this.span.style.visibility = "hidden";
    }
    show() {
        this.span.style.visibility = "visible";
    }
    toggle() {
        if (this.span.style.visibility === "hidden") {
            this.show();
        } else {
            this.hide();
        }
    }
    toggleDOM(map) {
        if (this.getMap()) {
            this.setMap(null);
        } else {
            this.setMap(map);
        }
    }
}