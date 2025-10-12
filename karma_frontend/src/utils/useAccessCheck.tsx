import { useState, useEffect } from 'react';
import axios from 'axios';

const useAccessCheck = (baseUrl) => {
  const [isAllowed, setIsAllowed] = useState(false);

  useEffect(() => {
    // Make an API call to check access
    axios
      .get(`${baseUrl}api/check-access/`)
      .then(() => {
        setIsAllowed(true);
      })
      .catch(() => alert('Access Denied.'));
  }, [baseUrl]);

  return isAllowed;
};

export default useAccessCheck;
