import React, { useEffect, useState } from "react";

function App() {
  const [weatherData, setWeatherData] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch("http://127.0.0.1:8000/api/weather")
      .then((res) => res.json())
      .then((data) => {
        setWeatherData(data);
        setLoading(false);
      })
      .catch((err) => {
        console.error("Error fetching weather:", err);
        setLoading(false);
      });
  }, []);

  return (
    <div style={{ textAlign: "center", padding: "20px", fontFamily: "Poppins" }}>
      <h1>🌦️ Weather Dashboard</h1>
      {loading ? (
        <p>Loading weather data...</p>
      ) : (
        <div
          style={{
            display: "flex",
            justifyContent: "center",
            flexWrap: "wrap",
            marginTop: "20px",
          }}
        >
          {weatherData.map((item, index) => (
            <div
              key={index}
              style={{
                border: "1px solid #ddd",
                borderRadius: "10px",
                padding: "20px",
                margin: "10px",
                width: "200px",
                background: "#f0f8ff",
                boxShadow: "2px 2px 6px rgba(0,0,0,0.1)",
              }}
            >
              <h2>{item.city}</h2>
              {item.data.main ? (
                <>
                  <p>🌡️ Temp: {item.data.main.temp} °C</p>
                  <p>☁️ {item.data.weather[0].description}</p>
                  <p>💧 Humidity: {item.data.main.humidity}%</p>
                </>
              ) : (
                <p>❌ Unable to fetch data</p>
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

export default App;
