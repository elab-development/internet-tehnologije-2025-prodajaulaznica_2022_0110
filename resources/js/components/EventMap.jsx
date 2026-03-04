import { MapContainer, TileLayer, Marker, Popup } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';

export default function EventMap({ event }) {
    const position = [event.latitude, event.longitude];

    return (
        <MapContainer
            center={position}
            zoom={13}
            className="h-96 rounded-xl border-2 border-slate-600"
        >
            <TileLayer
                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                attribution='&copy; OpenStreetMap'
            />
            <Marker position={position}>
                <Popup>
                    <strong>{event.title}</strong><br/>
                    {event.location}
                </Popup>
            </Marker>
        </MapContainer>
    );
}
