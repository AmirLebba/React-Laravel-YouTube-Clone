import React from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { useNavigate } from "react-router-dom";  // Import useNavigate from react-router-dom
import axios from "axios";

// Function to fetch videos
const fetchVideos = async () => {
  const response = await axios.get("http://localhost:8000/api/videos");
  return response.data;
};

// Function to delete a video
const deleteVideo = async (id) => {
  await axios.delete(`http://localhost:8000/api/videos/${id}`);
};

const VideoBrowser = () => {
  const queryClient = useQueryClient();
  const navigate = useNavigate();  // Use navigate for redirection

  // Query to fetch videos
  const { data: videos, isLoading, error } = useQuery({
    queryKey: ["videos"],
    queryFn: fetchVideos,
  });

  // Mutation to delete a video
  const deleteMutation = useMutation(deleteVideo, {
    onSuccess: () => {
      // Refresh the video list after deletion
      queryClient.invalidateQueries({ queryKey: ["videos"] });
    },
  });

  const handleDelete = (id) => {
    if (window.confirm("Are you sure you want to delete this video?")) {
      deleteMutation.mutate(id);
    }
  };

  const handlePlay = (id) => {
    // Navigate to the video player page when a video is clicked
    navigate(`/video/${id}`);
  };

  if (isLoading) return <div>Loading videos...</div>;
  if (error) return <div>Error: Unable to fetch videos.</div>;

  return (
    <div className="video-browser">
      {videos.length === 0 ? (
        <p>No videos available.</p>
      ) : (
        videos.map((video) => (
          <div
            key={video.id}
            className="video-card"
            style={styles.card}
            onClick={() => handlePlay(video.id)}  // Redirect to the video player page
          >
            <h3 style={styles.title}>{video.title}</h3>
            <video controls src={video.url} style={styles.video}></video>
            <button
              onClick={(e) => {
                e.stopPropagation();  // Prevent triggering the video play redirect
                handleDelete(video.id);
              }}
              style={styles.deleteButton}
            >
              Delete
            </button>
          </div>
        ))
      )}
    </div>
  );
};

// Styles for better UI
const styles = {
  card: {
    border: "1px solid #ccc",
    borderRadius: "8px",
    padding: "15px",
    marginBottom: "15px",
    width: "100%",
    maxWidth: "400px",
    boxShadow: "0 2px 5px rgba(0, 0, 0, 0.1)",
    cursor: "pointer",  // Add cursor pointer to indicate it's clickable
  },
  title: {
    fontSize: "16px",
    marginBottom: "10px",
    color: "#333",
  },
  video: {
    width: "100%",
    borderRadius: "4px",
  },
  deleteButton: {
    marginTop: "10px",
    backgroundColor: "red",
    color: "white",
    padding: "10px",
    border: "none",
    borderRadius: "4px",
    cursor: "pointer",
  },
};

export default VideoBrowser;
