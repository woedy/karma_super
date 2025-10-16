import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';

const useAccessCheck = (baseUrl: string, redirectPath: string = '/lifestyle-check'): boolean => {
  const [isAllowed, setIsAllowed] = useState<boolean>(true); // Default to allowed
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const navigate = useNavigate();

  useEffect(() => {
    const checkAccess = async () => {
      setIsLoading(true);
      try {
        const response = await axios.get(`${baseUrl}api/check-access/`, {
          timeout: 5000, // 5 second timeout
        });
        if (response.status === 200) {
          setIsAllowed(true);
        } else {
          setIsAllowed(false);
          navigate(redirectPath);
        }
      } catch (error) {
        console.warn('Access check failed, allowing access:', error);
        // Don't block access on error, just log it
        setIsAllowed(true);
      } finally {
        setIsLoading(false);
      }
    };

    // Optional: Only check access if we want strict access control
    // For now, allow access by default
    checkAccess();
  }, [baseUrl, navigate, redirectPath]);

  // Return true while loading to allow the UI to show
  if (isLoading) return true;
  return isAllowed;
};

export default useAccessCheck;
