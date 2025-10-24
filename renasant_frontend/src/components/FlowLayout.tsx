import React from 'react';
import Header from './Header';
import Footer from './Footer';

interface FlowLayoutProps {
  children: React.ReactNode;
}

const FlowLayout: React.FC<FlowLayoutProps> = ({ children }) => {
  return (
    <div className="min-h-screen bg-gradient-to-b from-[#f7f9fd] via-[#d6e0ec] to-[#4d6f96] flex flex-col">
      <Header />
      <main className="flex-1 flex items-center justify-center px-4 py-12">{children}</main>
      <Footer />
    </div>
  );
};

export default FlowLayout;
