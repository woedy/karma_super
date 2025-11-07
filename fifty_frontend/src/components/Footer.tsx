import React from 'react';

const footerLinks = [
  'About Us',
  'Customer Service',
  'Careers',
  'Investor Relations',
  'Media Center',
  'Security',
  'Privacy',
  'Site Map',
];

const Footer: React.FC = () => {
  return (
    <footer className="bg-[#f4f4f4] border-t border-gray-200">
      <div className="max-w-6xl mx-auto px-6 py-10 text-center">
        <div className="flex flex-wrap justify-center gap-x-4 gap-y-2 text-sm text-gray-700">
          {footerLinks.map((label, index) => (
            <React.Fragment key={label}>
              <a href="#" className="hover:text-[#003087] underline-offset-4 hover:underline">
                {label}
              </a>
              {index !== footerLinks.length - 1 && <span className="text-gray-400">|</span>}
            </React.Fragment>
          ))}
        </div>

        <p className="mt-5 text-sm text-gray-700">
          Copyright © 2025 Fifth Third Bank, National Association. All Rights Reserved. Member FDIC.{' '}
          <span className="inline-flex items-center gap-1 font-semibold">
            <span role="img" aria-label="house" className="text-base">🏠</span>
            Equal Housing Lender.
          </span>
        </p>

        <div className="mt-6 flex items-center justify-center">
          <img src="/assets/fifththird-logo.svg" alt="Fifth Third Logo" className="h-10 w-auto" />
        </div>
      </div>
    </footer>
  );
};

export default Footer;
