import { Link } from 'react-router-dom';

const Header2 = (props: {
  sidebarOpen: string | boolean | undefined;
  setSidebarOpen: (arg0: boolean) => void;
}) => {
  return (

    <div>
    <header className="sticky top-0 z-999 flex w-full h-7 bg-blue2 text-white text-sm drop-shadow-1 dark:bg-boxdark dark:drop-shadow-none">
      <div className="flex flex-grow items-center justify-between px-4 py-4 shadow-2 md:px-6 2xl:px-11">
 <p>EUR</p>
 <p>ENG</p>
 <p>+11 (0) 22 518 92 11</p>
 <div className='grid grid-cols-2 gap-1'> <p>About us</p>
 <p>Storage Solution</p>
       </div>

        
      </div>
    </header>
    <header className="bg-primary text-white py-4 w-full">
        <div className="max-w-screen-2xl mx-auto flex justify-between items-center px-4 xl:px-16">
          <h1 className="text-xl font-bold">GoldPalace</h1>
          <nav className="flex items-center space-x-6">
            <a href="#" className="text-sm">
              Home
            </a>
            <a href="#" className="text-sm">
              Our Products
            </a>
            <a href="#" className="text-sm">
             About Us
            </a>
            <a href="#" className="text-sm">
Contact Us         
   </a>
          </nav>
          <nav className='space-x-6'>

<Link to={'/signin'}>
<button className="text-sm">Sign In</button>


</Link>
          <button className="text-sm">Register</button>
          </nav>
     
        </div>
      </header>
    </div>
  );
};

export default Header2;
