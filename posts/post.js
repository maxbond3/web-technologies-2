const getPostId = () => {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get("id");
};

const fetchPost = async (postId) => {
  try {
    const response = await fetch(
      `https://jsonplaceholder.typicode.com/posts/${postId}`,
    );

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const post = await response.json();
    return post;
  } catch (error) {
    console.error("Error fetching post:", error);
    throw new Error("Failed to load post. Please try again later.");
  }
};

const fetchComments = async (postId) => {
  try {
    const response = await fetch(
      `https://jsonplaceholder.typicode.com/posts/${postId}/comments`,
    );

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const comments = await response.json();
    return comments;
  } catch (error) {
    console.error("Error fetching comments:", error);
    throw new Error("Failed to load comments. Please try again later.");
  }
};

const renderPost = (post) => {
  return `
        <h1 class="post-title">${escapeHtml(post.title)}</h1>
        <div class="post-body">
            <p>${escapeHtml(post.body)}</p>
        </div>
    `;
};

const renderComments = (comments) => {
  if (!comments || comments.length === 0) {
    return '<p class="no-comments">No comments yet.</p>';
  }

  return comments
    .map(
      (comment) => `
        <div class="comment">
            <div class="comment-header">
                <strong class="comment-name">${escapeHtml(comment.name)}</strong>
                <span class="comment-email">${escapeHtml(comment.email)}</span>
            </div>
            <p class="comment-body">${escapeHtml(comment.body)}</p>
        </div>
    `,
    )
    .join("");
};

const escapeHtml = (str) => {
  if (!str) return "";
  return str
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
};

const showError = (containerId, message) => {
  const container = document.getElementById(containerId);
  if (container) {
    container.innerHTML = `<div class="error-message">${escapeHtml(message)}</div>`;
  }
};

const loadPostData = async () => {
  const postId = getPostId();

  if (!postId) {
    showError("post-content", "Invalid post ID");
    showError("comments-container", "");
    return;
  }

  try {
    const [post, comments] = await Promise.all([
      fetchPost(postId),
      fetchComments(postId),
    ]);

    const postContainer = document.getElementById("post-content");
    if (postContainer) {
      postContainer.innerHTML = renderPost(post);
    }

    const commentsContainer = document.getElementById("comments-container");
    if (commentsContainer) {
      commentsContainer.innerHTML = renderComments(comments);
    }
  } catch (error) {
    console.error("Error loading post data:", error);
    showError("post-content", error.message || "Failed to load post");
    showError("comments-container", "");
  }
};

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", loadPostData);
} else {
  loadPostData();
}
