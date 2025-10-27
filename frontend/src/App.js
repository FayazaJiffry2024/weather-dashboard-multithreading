// src/App.js
import React, { useState, useEffect } from "react";
import "./App.css"; // Styling file

function App() {
  const [weatherData, setWeatherData] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch("http://127.0.0.1:8000/api/weather")
      .then(res => res.json())
      .then(data => {
        setWeatherData(data); // store data from backend
        setLoading(false);
      })
      .catch(err => {
        console.error("Error fetching weather:", err);
        setLoading(false);
      });
  }, []);

  if (loading) return <p>Loading weather data...</p>;

  return (
    <div className="app">
      <h1>🌤️ Weather Dashboard</h1>

      <div className="weather-cards">
        {weatherData.map(item => (
          <div key={item.city} className="card">
            <h2>{item.city}</h2>
            <img
              src={`http://openweathermap.org/img/wn/${item.icon}@2x.png`}
              alt={item.condition}
            />
            <p>Temperature: {item.temp}°C</p>
            <p>Feels like: {item.feels_like}°C</p>
            <p>Condition: {item.condition}</p>
            <p>Humidity: {item.humidity}%</p>
            <p>Wind: {item.wind_speed} m/s</p>
          </div>
        ))}
      </div>
    </div>
  );
}

export default App;
