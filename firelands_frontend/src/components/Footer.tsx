import React from 'react';

const Footer: React.FC = () => {
  return (
    <footer className="bg-white border-t border-gray-300 py-6">
      <div className="max-w-7xl mx-auto px-4">
        <div className="flex justify-between items-start">
          <div className="flex-1">
            <p className="text-xs text-gray-700 mb-2">
              Logix Federal Credit Union | Routing and Transit number — 322274187
            </p>
            <p className="text-xs text-gray-700 mb-3">
              Logix Online Banking is protected by reCAPTCHA and the{' '}
              <a href="#" className="text-blue-700 hover:underline">Google Privacy Policy</a> and the{' '}
              <a href="#" className="text-blue-700 hover:underline">Terms of Service</a> apply.
            </p>
            <p className="text-xs text-gray-700">
              © 2025 Logix Federal Credit Union. All Rights Reserved.
            </p>
            <div className="flex gap-3 text-xs text-blue-700 mt-2">
              <a href="#" className="hover:underline">Web Site</a>
              <span className="text-gray-400">|</span>
              <a href="#" className="hover:underline">Privacy</a>
              <span className="text-gray-400">|</span>
              <a href="#" className="hover:underline">Contact Us</a>
              <span className="text-gray-400">|</span>
              <a href="#" className="hover:underline">Join</a>
            </div>
          </div>
          <div className="flex flex-col items-end">
            <div className="w-16 h-16 border border-gray-300 flex items-center justify-center mb-2">
              <div className="text-center">
                <div className="text-xs font-bold text-gray-700">EQUAL</div>
                <div className="text-xs font-bold text-gray-700">HOUSING</div>
                <div className="text-xs font-bold text-gray-700">LENDER</div>
              </div>
            </div>
            <div className="text-xs font-bold text-gray-700 mb-1">NCUA</div>
            <p className="text-xs text-gray-700">Federally insured by NCUA</p>
          </div>
        </div>
        <div className="text-center text-xs text-gray-500 mt-6 border-t border-gray-200 pt-4">
          ~ Current time is 10/12/2025 5:35:36 PM ~ 0 ~ NWEB02 ~
        </div>
      </div>
    </footer>
  );
};

export default Footer;
