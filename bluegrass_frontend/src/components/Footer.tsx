import React from 'react';

const Footer: React.FC = () => {
  return (
    <footer className="bg-white border-t border-gray-300 py-6">
      <div className="max-w-7xl mx-auto px-4">
        <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
          <div className="flex items-center gap-6 justify-center md:justify-start">
            <img
              src="/assets/equal-housing.png"
              alt="Equal Housing Lender"
              className="h-12 w-auto"
            />
            <img
              src="/assets/ncua.png"
              alt="National Credit Union Administration"
              className="h-12 w-auto"
            />
          </div>
          <div className="text-xs text-gray-700 text-center md:text-right space-y-2">
            <p>© 2025 Bluegrass Community FCU. All Rights Reserved.</p>
            <p>
              This site contains links to other sites on the Internet. We, and your credit union,
              cannot be responsible for the content or privacy policies of these other sites.
            </p>
            <p>Version: v26.10.22.0</p>
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
