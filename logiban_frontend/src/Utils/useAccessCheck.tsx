import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';

const useAccessCheck = (baseUrl: string, redirectPath: string = '/lifestyle-check'): boolean => {
  const [isAllowed, setIsAllowed] = useState<boolean>(false);
  const [isLoading, setIsLoading] = useState<boolean>(true);
  const navigate = useNavigate();

  useEffect(() => {
    const checkAccess = async () => {
      try {
        await axios.get(`${baseUrl}api/check-access/`);
        setIsAllowed(true);
      } catch (error) {
        console.error('Access check failed:', error);
        setIsAllowed(false);
        // Redirect to demo page when access is denied
        navigate(redirectPath);
      } finally {
        setIsLoading(false);
      }
    };

    checkAccess();
  }, [baseUrl, navigate, redirectPath]);

  // Return false while loading to prevent flash of protected content
  // This ensures the protected content is only shown after access is confirmed
  if (isLoading) return false;
  return isAllowed;
};

export default useAccessCheck;
