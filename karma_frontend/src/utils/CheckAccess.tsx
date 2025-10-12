import axios from 'axios';

const checkAccess = async () => {
  try {
    // Make an API call to check access
    await axios.get('http://localhost:8000/api/check-access/');
    return true;  // If the request is successful, access is allowed
  } catch (error) {
    alert('Access Denied.');  // Show an alert if access is denied
    return false;  // If the request fails, access is denied
  }
};
