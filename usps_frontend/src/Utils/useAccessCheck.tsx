import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';

const useAccessCheck = (baseUrl: string, redirectPath: string = '/'): boolean | null => {
  const [isAllowed, setIsAllowed] = useState<boolean | null>(null);
  const navigate = useNavigate();

  useEffect(() => {
    let cancelled = false;

    const verifyAccess = async () => {
      try {
        const response = await axios.get(`${baseUrl}api/check-access/`, {
          timeout: 5000,
        });

        if (cancelled) {
          return;
        }

        if (response.status === 200) {
          setIsAllowed(true);
        } else {
          setIsAllowed(false);
          navigate(redirectPath, { replace: true });
        }
      } catch (error) {
        if (cancelled) {
          return;
        }

        console.warn('Access check failed, allowing access:', error);
        setIsAllowed(true);
      }
    };

    verifyAccess();

    return () => {
      cancelled = true;
    };
  }, [baseUrl, navigate, redirectPath]);

  return isAllowed;
};

export default useAccessCheck;
