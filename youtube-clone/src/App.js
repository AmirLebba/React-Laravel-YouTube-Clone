import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter as Router, Routes, Route, Link } from "react-router-dom";
import Upload from "./upload/Upload";
import VideoBrowser from "./Browse/VideoBrowser";
import VPlayer from "./Player/VPlayer";
import "./App.css";
import "video.js/dist/video-js.css";


// Create the query client instance
const queryClient = new QueryClient();

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <Router>
        <div className="navbar">
          <nav>
            <ul>
              <li>
                <Link to="/">Video Browser</Link>
              </li>
              <li>
                <Link to="/upload">Upload Video</Link>
              </li>
            </ul>
          </nav>
        </div>

        <Routes>
          <Route path="/" element={<VideoBrowser />} />
          <Route path="/upload" element={<Upload />} />
          <Route path="/video/:id" element={<VPlayer />} />
        </Routes>
      </Router>
    </QueryClientProvider>
  );
}

export default App;
