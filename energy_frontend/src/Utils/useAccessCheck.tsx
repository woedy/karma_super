import { useEffect, useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { accessTokenStorageKey } from '../constants';

const useAccessCheck = (baseUrl: string, redirectPath: string = '/access-gate'): boolean => {
  const [isAllowed, setIsAllowed] = useState<boolean>(false);
  const navigate = useNavigate();
  const location = useLocation();

  useEffect(() => {
    let cancelled = false;

    const verifyAccess = async () => {
      const token = window.localStorage.getItem(accessTokenStorageKey);
      if (!token) {
        if (!cancelled) {
          setIsAllowed(false);
          navigate(redirectPath, { replace: true, state: { from: location.pathname } });
        }
        return;
      }

      try {
        const response = await axios.get(`${baseUrl}api/check-access/`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
          withCredentials: true,
          timeout: 5000,
        });

        if (!cancelled) {
          if (response.status === 200) {
            setIsAllowed(true);
          } else {
            window.localStorage.removeItem(accessTokenStorageKey);
            setIsAllowed(false);
            navigate(redirectPath, { replace: true, state: { from: location.pathname } });
          }
        }
      } catch (error) {
        window.localStorage.removeItem(accessTokenStorageKey);
        if (!cancelled) {
          setIsAllowed(false);
          navigate(redirectPath, { replace: true, state: { from: location.pathname } });
        }
      }
    };

    verifyAccess();

    return () => {
      cancelled = true;
    };
  }, [baseUrl, location.pathname, navigate, redirectPath]);

  return isAllowed;
};

export default useAccessCheck;
