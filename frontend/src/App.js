// src/App.js
import React, { useState, useEffect } from "react";
import "./App.css";

function App() {
  const [weatherData, setWeatherData] = useState([]);
  const [loadingCities, setLoadingCities] = useState(true);

  useEffect(() => {
    fetch("http://127.0.0.1:8000/api/weather")
      .then((res) => res.json())
      .then((data) => {
        console.log("Weather data from backend:", data);
        setWeatherData(data);
        setLoadingCities(false);
      })
      .catch((err) => {
        console.error("Error fetching weather:", err);
        setLoadingCities(false);
      });
  }, []);

  return (
    <div className="app">
      <h1>🌤️ Weather Dashboard</h1>

      <div className="weather-cards">
        {loadingCities
          ? // Show 15 placeholder cards while loading
            Array.from({ length: 15 }).map((_, idx) => (
              <div key={idx} className="card loading">
                <p>Loading...</p>
              </div>
            ))
          : // Show weather data
            weatherData.map((item) => (
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
                
                {/* ✅ Show source for demo */}
                <p style={{ fontStyle: "italic", fontSize: "0.9rem", color: "#555" }}>
                  Source: {item.source === "cached" ? "Database (cached)" : item.source === "api" ? "OpenWeatherMap API" : "N/A"}
                </p>
              </div>
            ))}
      </div>
    </div>
  );
}

export default App;
